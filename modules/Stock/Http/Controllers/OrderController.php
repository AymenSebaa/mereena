<?php

namespace Modules\Stock\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Stock\Models\Order;
use Modules\Stock\Models\OrderItem;
use Modules\Stock\Models\Product;
use Modules\Stock\Models\Inventory;

class OrderController extends Controller {

    public function index() {
        $orders = Order::with(['items.product', 'status'])->latest()->get();
        return view('stock::orders.index', compact('orders'));
    }

    public function store(Request $request) {
        $request->validate([
            'company_id' => 'required',
            'status_id' => 'required',
            'items' => 'required|array|min:1',
        ]);

        $order = Order::create([
            'company_id' => $request->company_id,
            'status_id' => $request->status_id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'billing_address' => $request->billing_address,
            'total' => 0,
        ]);

        $total = 0;
        foreach ($request->items as $item) {
            $inventory = Inventory::find($item['inventory_id']);
            if ($inventory && $item['quantity'] > $inventory->quantity) {
                return response()->json(['error' => 'Quantity exceeds available stock for ' . $inventory->product->name], 422);
            }

            $subtotal = $item['quantity'] * $item['unit_price'];
            $total += $subtotal;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'inventory_id' => $item['inventory_id'],
                'supplier_id' => $inventory?->supplier_id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $subtotal,
            ]);

            if ($inventory) {
                $inventory->decrement('quantity', $item['quantity']);
            }
        }

        $order->update(['total' => $total]);

        return response()->json(['result' => true, 'order' => $order->load('items')]);
    }

    public function delete($id) {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(['result' => true]);
    }
}
