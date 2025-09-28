<?php

namespace Modules\Stock\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Stock\Models\Inventory;
use Modules\Stock\Models\Product;
use Modules\Stock\Models\Supplier;

class InventoryController extends Controller {
    /**
     * Display a listing of inventories.
     */
    public function index() {
        $inventories = Inventory::with(['product', 'supplier'])->get();
        $products    = Product::all();
        $suppliers   = Supplier::all();

        return view('stock::inventories.index', compact('inventories', 'products', 'suppliers'));
    }

    /**
     * Store or update an inventory.
     */
    public function upsert(Request $request, $id = null) {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'price'      => 'required|numeric|min:0',
            'quantity'   => 'required|numeric|min:0',
            'made_at'    => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:made_at',
        ]);

        $inventory = Inventory::updateOrCreate(
            ['id' => $request->input('id') ?? $id],
            $validated
        );

        $inventory->load(['product', 'supplier']);

        return response()->json([
            'result'  => true,
            'message' => $request->input('id') ? 'Inventory updated successfully' : 'Inventory created successfully',
            'inventory' => $inventory,
        ]);
    }

    /**
     * Remove the specified inventory.
     */
    public function delete($id) {
        $inventory = Inventory::find($id);
        if ($inventory) $inventory->delete();

        return response()->json([
            'result'  => true,
            'message' => 'Inventory deleted successfully',
            'id'      => $id,
        ]);
    }

    public function byProduct($productId) {
        $inventories = Inventory::with('supplier')
            ->where('product_id', $productId)
            ->get();

        return response()->json($inventories);
    }
}
