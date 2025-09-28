<?php

namespace Modules\Example\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Example\Models\Item;

class ItemController extends Controller {
    /**
     * Display a listing of inventories.
     */
    public function index() {
        $data['items'] = Item::with(['relation1', 'relation2'])->get();

        return view('example::items.index', $data);
    }

    /**
     * Store or update an example.
     */
    public function upsert(Request $request, $id = null) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:types,id',
            'price'      => 'required|numeric|min:0',
            'expires_at' => 'nullable|date|after_or_equal:made_at',
        ]);

        $example = Item::updateOrCreate(
            ['id' => $request->input('id') ?? $id],
            $validated
        );

        $example->load(['relation1', 'relation2']);

        return response()->json([
            'result'  => true,
            'message' => $request->input('id') ? 'Example updated successfully' : 'Example created successfully',
            'example' => $example,
        ]);
    }

    /**
     * Remove the specified item.
     */
    public function delete($id) {
        $item = Item::find($id);
        if ($item) $item->delete();

        return response()->json([
            'result'  => true,
            'message' => 'Item deleted successfully',
            'id'      => $id,
        ]);
    }
}
