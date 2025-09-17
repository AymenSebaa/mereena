<?php

namespace Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Product\Entities\Supplier;

class SupplierController extends Controller {
    /**
     * Display a listing of suppliers.
     */
    public function index(Request $request) {
        $suppliers = Supplier::query()
            ->when(
                $request->search,
                fn($q, $search) =>
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
            )
            ->paginate(20);

        return response()->json($suppliers);
    }

    /**
     * Store a newly created supplier.
     */
    public function store(Request $request) {
        $data = $request->validate([
            'company_id'   => 'required|exists:companies,id',
            'name'         => 'required|string|max:255',
            'email'        => 'nullable|email',
            'phone'        => 'nullable|string|max:50',
            'address'      => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
        ]);

        $supplier = Supplier::create($data);

        return response()->json([
            'message'  => 'Supplier created successfully',
            'supplier' => $supplier
        ], 201);
    }

    /**
     * Show a supplier.
     */
    public function show(Supplier $supplier) {
        return response()->json($supplier->load('stocks'));
    }

    /**
     * Update a supplier.
     */
    public function update(Request $request, Supplier $supplier) {
        $data = $request->validate([
            'name'         => 'sometimes|required|string|max:255',
            'email'        => 'nullable|email',
            'phone'        => 'nullable|string|max:50',
            'address'      => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
        ]);

        $supplier->update($data);

        return response()->json([
            'message'  => 'Supplier updated successfully',
            'supplier' => $supplier
        ]);
    }

    /**
     * Delete a supplier.
     */
    public function destroy(Supplier $supplier) {
        $supplier->delete();

        return response()->json([
            'message' => 'Supplier deleted successfully'
        ]);
    }
}
