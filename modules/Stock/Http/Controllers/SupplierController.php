<?php

namespace Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Stock\Models\Supplier;

class SupplierController extends Controller {
    /**
     * Display a listing of suppliers.
     */
    public function index(Request $request) {
        $query = Supplier::query();

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        }

        $suppliers = $query->latest()->get();

        if ($request->wantsJson()) {
            return response()->json([
                'result' => true,
                'data'   => $suppliers,
            ]);
        }

        return view('stock::suppliers.index', compact('suppliers'));
    }

    /**
     * Store or update a supplier.
     */
    public function upsert(Request $request, $id = null) {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        $supplier = Supplier::updateOrCreate(
            ['id' => $request->input('id') ?? $id],
            $validated
        );

        return response()->json([
            'result'  => true,
            'message' => $request->input('id') ? 'Supplier updated successfully' : 'Supplier created successfully',
            'data'    => $supplier,
        ]);
    }

    /**
     * Remove the specified supplier.
     */
    public function delete($id) {
        $supplier = Supplier::find($id);
        if ($supplier) $supplier->delete();

        return response()->json([
            'result'  => true,
            'message' => 'Supplier deleted successfully',
            'id'      => $id,
        ]);
    }
}
