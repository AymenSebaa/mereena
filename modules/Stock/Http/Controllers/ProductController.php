<?php

namespace Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Stock\Models\Product;

class ProductController extends Controller {
    public function index(Request $request) {
        $query = Product::with(['company', 'category']);

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('sku', 'like', "%$search%")
                    ->orWhere('brand', 'like', "%$search%");
            });
        }

        $data['products'] = $query->latest()->get();

        if ($request->wantsJson()) {
            return response()->json([
                'result' => true,
                'data'   => $data['products']
            ]);
        }

        $data['types'] = Type::where('name', 'Products')->first()->subTypes ?? collect();

        return view('stock::products.index', $data);
    }

    public function upsert(Request $request, $id = null) {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'sku'         => 'required|string|max:255|unique:products,sku,' . ($request->id ?? $id),
            'category_id' => 'required|integer|exists:types,id',
            'brand'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'images.*'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        // remove images from validated (we handle them manually)
        unset($validated['images']);

        $product = Product::updateOrCreate(
            ['id' => $request->input('id') ?? $id],
            $validated
        );

        // force correct public path
        $publicPath = base_path('../../public_html/mereena');

        if ($request->hasFile('images')) {
            Log::info('Images received', [
                'count' => count($request->file('images')),
                'files' => array_map(fn($f) => $f->getClientOriginalName(), $request->file('images'))
            ]);

            // dd($request->file('images'), $product->images);

            // Delete old images
            if (is_array($product->images)) {
                foreach ($product->images as $old) {
                    $oldPath = $publicPath . '/' . $old;
                    if (is_file($oldPath)) {
                        unlink($oldPath);
                    }
                }
            }

            // Ensure upload directory exists
            $uploadDir = $publicPath . '/uploads/products';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            // Save new images
            $paths = [];
            foreach ($request->file('images') as $file) {
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);
                $paths[] = 'uploads/products/' . $filename;
            }

            $product->images = $paths;
            $product->save();
        } else {
            Log::warning('No images received!');
        }

        // reload product with relations
        $product->load(['category:id,name', 'company:id,name']);

        return response()->json([
            'result'  => true,
            'message' => $request->input('id')
                ? 'Product updated successfully'
                : 'Product created successfully',
            'data'    => $product,
        ]);
    }

    public function delete($id) {
        $product = Product::find($id);
        if ($product) {
            if ($product->images) {
                foreach ($product->images as $img) {
                    $imgPath = public_path($img);
                    if (file_exists($imgPath)) unlink($imgPath);
                }
            }
            $product->delete();
        }

        return response()->json([
            'result'  => true,
            'message' => 'Product deleted successfully',
            'id'      => $id,
        ]);
    }
}
