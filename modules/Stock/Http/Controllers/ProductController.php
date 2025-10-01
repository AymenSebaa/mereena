<?php

namespace Modules\Stock\Http\Controllers;

use App\Http\Controllers\BaseCrudController;
use Modules\Stock\Models\Product;

class ProductController extends BaseCrudController {
    protected string $modelClass = Product::class;
    protected string $viewPrefix = 'stock::products';
    protected array $with = ['company:id,name', 'category:id,name'];
    protected array $imageFields = ['images'];
    protected string $uploadPath = 'uploads/products';
    protected array $searchable = ['name', 'sku', 'brand'];

    protected function rules(): array {
        return [
            'name'        => 'required|string|max:255',
            'sku'         => 'required|string|max:255|unique:products,sku,' . request()->id,
            'category_id' => 'required|integer|exists:types,id',
            'brand'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            // 'images.*'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ];
    }

    protected function label(): string {
        return 'Product';
    }
}
