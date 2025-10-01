@section('extra-fields')
    <x-preview-upload name="images" label="Product Images" />

    <div class="mb-3">
        <label>Name</label>
        <input type="text" class="form-control rounded-pill" name="name" id="product_name" required>
    </div>

    <div class="mb-3">
        <label>SKU</label>
        <input type="text" class="form-control rounded-pill" name="sku" id="product_sku" required>
    </div>

    <x-select-search id="category_input" hiddenId="category_id" label="Product Category" name="category_id" placeholder="Search product category..."
        parentName="Products" fetchUrl="{{ oRoute('types.search') }}" />

    <div class="mb-3">
        <label>Brand</label>
        <input type="text" class="form-control rounded-pill" name="brand" id="product_brand">
    </div>

    <div class="mb-3">
        <label>Description</label>
        <textarea class="form-control rounded-pill" name="description" id="product_description"></textarea>
    </div>
@endsection

@section('extra-fill')
    $("#product_name").val(item.name);
    $("#product_sku").val(item.sku);
    setSelectSearchValue('category_input_wrapper', item.category_id, item.category.name);
    $("#product_brand").val(item.brand);
    $("#product_description").val(item.description);

    if (item.images && item.images.length) {
        if (typeof uploaders !== "undefined") {
            // Reset uploader
            uploaders.forEach(u => u.removeAllFiles());
            // Add mock images
            item.images.forEach(img => {
                uploaders[0].addMockFile(img); // Assumes first uploader is product_images
            });
        }
    }
@endsection
