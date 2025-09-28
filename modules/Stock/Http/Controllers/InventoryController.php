<?php

namespace Modules\Stock\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Product\Entities\Supplier;
use Modules\Stock\Models\Inventory;
use Modules\Stock\Models\Product;

class InventoryController extends Controller {
    public function index() {
        $data['inventories'] = Inventory::with(['product', 'supplier'])->get();
        $data['products']    = Product::all();
        $data['suppliers']   = Supplier::all();

        return view('stock::inventories.index', $data);
    }

    public function upsert(Request $request) {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'quantity'   => ['required', 'numeric', 'min:0'],
            'made_at'    => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:made_at'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        if ($request->id) {
            $inventory = Inventory::findOrFail($request->id);
            $message = 'Inventory updated successfully';
        } else {
            $inventory = new Inventory();
            $message = 'Inventory created successfully';
        }

        $inventory->product_id  = $request->product_id;
        $inventory->supplier_id = $request->supplier_id;
        $inventory->quantity    = $request->quantity;
        $inventory->made_at     = $request->made_at;
        $inventory->expires_at  = $request->expires_at;

        $inventory->save();

        session()->flash('success', $message);
        return response()->json(['result' => $inventory]);
    }

    public function delete($id) {
        $inventory = Inventory::find($id);
        if ($inventory) {
            $inventory->delete();
        }
        return back()->with('success', 'Inventory deleted successfully');
    }
}
