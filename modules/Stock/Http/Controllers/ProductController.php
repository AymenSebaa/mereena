<?php

namespace Modules\Stock\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Product\Entities\Supplier;
use Modules\Stock\Models\Product;

class ProductController extends Controller {
    public function index() {
        $data['products'] = Product::with('company')->get();
        $data['suppliers'] = Supplier::all(); 

        return view('stock::products.index', $data);
    }

    public function upsert(Request $request) {
        $validator = Validator::make($request->all(), [
            'company_id' => ['required', 'exists:companies,id'],
            'name'       => ['required', 'string', 'max:255'],
            'image'      => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string'],
            'status'     => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        if ($request->id) {
            $product = Product::findOrFail($request->id);
            $message = 'Product updated successfully';
        } else {
            $product = new Product();
            $message = 'Product created successfully';
        }

        $product->company_id  = $request->company_id;
        $product->name        = $request->name;
        $product->description = $request->description;
        $product->status      = $request->status ?? true;

        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/products'), $filename);
            $product->image = $filename;
        }

        $product->save();

        session()->flash('success', $message);
        return response()->json(['result' => $product]);
    }

    public function delete($id) {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
        }
        return back()->with('success', 'Product deleted successfully');
    }
}
