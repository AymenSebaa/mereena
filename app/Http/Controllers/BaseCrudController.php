<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

abstract class BaseCrudController extends Controller {
    protected string $modelClass;
    protected string $viewPrefix;
    protected array $defaults = [];
    protected array $with = [];
    protected array $orderBy = ['created_at' => 'desc'];
    protected array $searchable = ['name'];
    protected array $imageFields = []; // specify which fields are image arrays
    protected string $uploadPath = 'uploads'; // base upload path

    public function index(Request $request) {
        $isAjax = $request->ajax() || $request->wantsJson();
        $query  = ($this->modelClass)::query();

        if (!empty($this->with)) $query->with($this->with);
        foreach ($this->orderBy as $col => $dir) $query->orderBy($col, $dir);

        $items = $query->get();

        if ($isAjax) return response()->json($items);

        return view("{$this->viewPrefix}.index", ['defaults' => $this->defaults]);
    }

    public function search(Request $request) {
        $q = $request->q;
        $query = ($this->modelClass)::query();

        if (!empty($this->with)) {
            $query->with($this->with);
        }

        $query->where(function ($builder) use ($q) {
            foreach ($this->searchable as $column) {
                if (str_contains($column, '.')) {
                    // Handle relation search: e.g. profile.phone
                    [$relation, $field] = explode('.', $column, 2);
                    $builder->orWhereHas($relation, function ($rel) use ($field, $q) {
                        $rel->where($field, 'like', "%{$q}%");
                    });
                } else {
                    // Normal column search
                    $builder->orWhere($column, 'like', "%{$q}%");
                }
            }
        });

        $results = $query->limit(20)->get();
        return response()->json($results);
    }

    public function upsert(Request $request, $id = null) {
        $validated = $request->validate($this->rules());

        foreach ($this->imageFields as $field) {
            if ($request->has($field)) {
                $validated[$field] = $this->handleImages($request->input($field), $field, $id);
            }
        }

        $item = ($this->modelClass)::updateOrCreate(
            ['id' => $request->input('id') ?? $id],
            array_merge($validated, $this->defaults)
        );

        if (!empty($this->with)) $item->load($this->with);

        return response()->json([
            'result'  => true,
            'message' => $id
                ? "{$this->label()} {$item->name} updated successfully"
                : "{$this->label()} {$item->name} created successfully",
            'data'    => $item,
        ]);
    }

    protected function handleImages(array $base64Images, string $field, $id = null): array {
        $uploadDir = "../../public_html/mereena/{$this->uploadPath}";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

        $paths = [];
        foreach ($base64Images as $b64) {
            if (preg_match('/^data:image\/(\w+);base64,/', $b64, $type)) {
                $b64 = substr($b64, strpos($b64, ',') + 1);
                $ext = strtolower($type[1]); // jpg, png, gif
                $data = base64_decode($b64);
                $filename = uniqid() . '.' . $ext;
                file_put_contents("{$uploadDir}/{$filename}", $data);
                $paths[] = "{$this->uploadPath}/{$filename}";
            }
        }

        // delete old images if updating
        if ($id) {
            $item = ($this->modelClass)::find($id);
            if ($item && !empty($item->{$field}) && is_array($item->{$field})) {
                foreach ($item->{$field} as $old) {
                    $oldPath = public_path($old);
                    if (is_file($oldPath)) unlink($oldPath);
                }
            }
        }

        return $paths;
    }

    public function delete(Request $request) {
        $item = ($this->modelClass)::findOrFail($request->id);

        // delete images
        foreach ($this->imageFields as $field) {
            if (!empty($item->{$field}) && is_array($item->{$field})) {
                foreach ($item->{$field} as $img) {
                    $imgPath = public_path($img);
                    if (file_exists($imgPath)) unlink($imgPath);
                }
            }
        }

        $item->delete();

        return response()->json([
            'result'  => true,
            'message' => "{$this->label()} {$item->name} deleted successfully",
        ]);
    }

    abstract protected function rules(): array;
    abstract protected function label(): string;
}
