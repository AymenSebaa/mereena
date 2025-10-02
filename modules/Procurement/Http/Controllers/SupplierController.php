<?php

namespace Modules\Procurement\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseCrudController;
use Modules\Procurement\Models\Supplier;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\Profile;

class SupplierController extends BaseCrudController {

    protected string $modelClass = Supplier::class;
    protected string $viewPrefix = 'procurement::suppliers';
    protected array $searchable = ['name', 'email', 'profile.phone'];
    protected array $defaults = [];
    protected array $with = ['profile', 'profile.role'];

    protected function rules(): array {
        $supplier = Supplier::find(request()->id);
        return [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255|unique:users,email,' . $supplier?->id ?? null,
            'phone'   => 'nullable|string|max:50|unique:profiles,phone,' . $supplier?->profile->id ?? null,
            'address' => 'nullable|string|max:500',
        ];
    }

    protected function label(): string {
        return 'Supplier';
    }

    public function upsert(Request $request, $id = null) {
        $validated = $request->validate($this->rules());

        // handle images
        foreach ($this->imageFields as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $this->handleImages($request->file($field), $field, $id);
            }
        }

        $request->role_id = Role::where('name', 'Supplier')->first()->id ?? 11; // fallback role

        $request->merge([
            'email' => strtolower(trim($request->email)),
            'name'  => trim($request->name),
        ]);

        $supplier = Supplier::updateOrCreate(
            ['id' => $request->id],
            [
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]
        );

        $supplier->profile = Profile::updateOrCreate(
            ['user_id' => $supplier->id],
            [
                'role_id'    => $request->role_id,
                'country_id' => $request->country_id,
                'site_id'    => $request->site_id,
                'category'   => $request->category ?? '',
                'phone'      => $request->phone,
                'address'    => $request->address,
            ]
        );

        if (!empty($this->with)) $supplier->load($this->with);

        return response()->json([
            'result'  => true,
            'message' => $id
                ? "{$this->label()} {$supplier->name} updated successfully"
                : "{$this->label()} {$supplier->name} created successfully",
            'data'    => $supplier,
        ]);
    }
}
