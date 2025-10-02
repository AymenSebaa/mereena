<?php

namespace Modules\Stock\Http\Controllers;

use App\Http\Controllers\BaseCrudController;
use Modules\Stock\Models\Inventory;

class InventoryController extends BaseCrudController {
    protected string $modelClass = Inventory::class;
    protected string $viewPrefix = 'stock::inventories';
    protected array $with = ['product', 'supplier'];

    protected function rules(): array {
        return [
            'product_id'  => 'required|exists:products,id',
            'supplier_id' => 'required|exists:users,id',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|numeric|min:0',
            'made_at'     => 'nullable|date',
            'expires_at'  => 'nullable|date|after_or_equal:made_at',
        ];
    }

    protected function label(): string {
        return 'Inventory';
    }

    /**
     * Return inventories for a specific product
     */
    public function byProduct($productId) {
        $inventories = Inventory::with('supplier')
            ->where('product_id', $productId)
            ->get();

        return response()->json($inventories);
    }
}
