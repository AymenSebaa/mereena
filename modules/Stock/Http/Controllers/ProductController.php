<?php

namespace Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Stock\Models\Product;

class ProductController extends Controller {
    public function index(Request $request) {
        $query = Product::query();

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('sku', 'like', "%$search%")
                    ->orWhere('brand', 'like', "%$search%")
                    ->orWhere('category', 'like', "%$search%");
            });
        }

        $products = $query->latest()->get();

        if ($request->wantsJson()) {
            return response()->json(['result' => true, 'data' => $products]);
        }

        return view('stock::products.index', compact('products'));
    }

    public function upsert(Request $request, $id = null) {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'sku'         => 'required|string|max:255|unique:products,sku,' . $request->id,
            'category'    => 'nullable|string|max:255',
            'brand'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'images.*'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $product = Product::updateOrCreate(
            ['id' => $request->input('id') ?? $id],
            $validated
        );

        // Upload Images
        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $file) {
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/products'), $filename);
                $paths[] = 'uploads/products/' . $filename;
            }

            $existing = $product->images ?? [];
            $product->images = array_merge($existing, $paths);
            $product->save();
        }

        return response()->json([
            'result'  => true,
            'message' => $request->input('id') ? 'Product updated successfully' : 'Product created successfully',
            'data'    => $product,
        ]);
    }



    public function delete($id) {
        $product = Product::find($id);
        if ($product) $product->delete();

        return response()->json([
            'result'  => true,
            'message' => 'Product deleted successfully',
            'id'      => $id,
        ]);
    }
}
