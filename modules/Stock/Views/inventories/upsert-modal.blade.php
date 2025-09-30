@section('extra-fields')
    <x-select-search id="product_input" hiddenId="product_id" name="product_id" label="Product"
        fetchUrl="{{ oRoute('stock.products.search') }}" />

    <x-select-search id="supplier_input" hiddenId="supplier_id" label="Supplier" name="supplier_id" placeholder="Search supplier..."
        fetchUrl="{{ oRoute('stock.suppliers.search') }}" />
    <div class="mb-3">
        <label>Price</label>
        <input type="number" class="form-control rounded-pill" name="price" id="inventory_price" required>
    </div>

    <div class="mb-3">
        <label>Quantity</label>
        <input type="number" class="form-control rounded-pill" name="quantity" id="inventory_quantity" required>
    </div>

    <div class="mb-3">
        <label>Made At</label>
        <input type="date" class="form-control rounded-pill" name="made_at" id="inventory_made_at">
    </div>

    <div class="mb-3">
        <label>Expires At</label>
        <input type="date" class="form-control rounded-pill" name="expires_at" id="inventory_expires_at">
    </div>
@endsection

@section('extra-fill')
    setSelectSearchValue('product_input_wrapper', item.product_id, item.product.name);
    // setSelectSearchValue('supplier_input_wrapper', item.supplier_id, item.supplier.name);
    $("#inventory_price").val(item.price);
    $("#inventory_quantity").val(item.quantity);
    $("#inventory_made_at").val(item.made_at);
    $("#inventory_expires_at").val(item.expires_at);
@endsection
