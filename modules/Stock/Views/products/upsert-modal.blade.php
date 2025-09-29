<button class="btn btn-primary rounded-pill w-50 m-3" data-bs-toggle="modal" data-bs-target="#upsertProductModal" onclick="resetUpsertForm()">
    <i class="bi bi-plus-circle me-2"></i> New
</button>

<!-- Upsert Modal -->
<div class="modal fade" id="upsertProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Upsert Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="mb-3">
                <div class="progress d-none" id="uploadProgressWrapper">
                    <div id="uploadProgress" class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                        role="progressbar" style="width:0%">0%</div>
                </div>
            </div>
            <form id="upsertProductForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="product_id">
                <div class="modal-body">
                    <div id="formAlert" class="alert d-none" role="alert"></div>

                    @include('partials.preview-upload', ['label' => 'Product Images'])

                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control rounded-pill" name="name" id="product_name" required>
                    </div>
                    <div class="mb-3">
                        <label>SKU</label>
                        <input type="text" class="form-control rounded-pill" name="sku" id="product_sku" required>
                    </div>
                    <div class="mb-3">
                        <label>Type</label>
                        <select class="form-control rounded-pill" name="category_id" id="product_category_id" required>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger category_id-error"></span>
                    </div>
                    <div class="mb-3">
                        <label>Brand</label>
                        <input type="text" class="form-control rounded-pill" name="brand" id="product_brand">
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea class="form-control rounded-pill" name="description" id="product_description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark rounded-pill" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="saveBtn" class="btn btn-success rounded-pill">
                        <span id="saveBtnText">Save Product</span>
                        <span id="saveBtnSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetUpsertForm() {
    $("#upsertProductForm")[0].reset();
    $("#product_id").val("");
    $("#formAlert").addClass("d-none").text("");
    $("#uploadProgressWrapper").addClass("d-none");
    $("#uploadProgress").css("width","0%").text("0%");
    if (typeof uploaders !== "undefined") {
        uploaders.forEach(u => u.clearFiles && u.clearFiles());
    }
}

$("#upsertProductForm").on("submit", function(e){
    e.preventDefault();
    let formData = new FormData(this);

    $("#saveBtn").prop("disabled",true);
    $("#saveBtnText").text("Saving...");
    $("#saveBtnSpinner").removeClass("d-none");
    $("#formAlert").addClass("d-none");

    if (typeof uploaders !== "undefined") {
        uploaders.forEach(u => u.getFiles().forEach(f => formData.append("images[]", f)));
    }

    $("#uploadProgressWrapper").removeClass("d-none");
    $("#uploadProgress").css("width","0%").text("0%");

    $.ajax({
        url: "{{ oRoute('stock.products.upsert') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        xhr: function(){
            let xhr = $.ajaxSettings.xhr();
            if (xhr.upload) {
                xhr.upload.addEventListener("progress", e=>{
                    if (e.lengthComputable) {
                        let p = Math.round((e.loaded / e.total) * 100);
                        $("#uploadProgress").css("width",p+"%").attr("aria-valuenow",p).text(p+"%");
                    }
                });
            }
            return xhr;
        },
        success: function(data){
            if (data.result) {
                let card = productCard(data.data);
                let existing = $("#product_"+data.data.id);
                existing.length ? existing.replaceWith(card) : $("#products-container").prepend(card) && products.unshift(data.data);

                $("#formAlert").removeClass("d-none").addClass("alert-success").text(data.message);
                setTimeout(()=>{
                    bootstrap.Modal.getInstance($("#upsertProductModal")[0]).hide();
                    resetUpsertForm();
                },1500);
            } else {
                throw new Error(data.message || "Something went wrong");
            }
        },
        error: function(xhr){
            $("#formAlert").removeClass("d-none").addClass("alert-danger").text(xhr.responseJSON?.message || "Upload failed");
            $("#uploadProgressWrapper").addClass("d-none");
        },
        complete: function(){
            $("#saveBtn").prop("disabled",false);
            $("#saveBtnText").text("Save Product");
            $("#saveBtnSpinner").addClass("d-none");
        }
    });
});

function openEdit(id,name,sku,category_id,brand,price,description){
    resetUpsertForm();
    $("#product_id").val(id);
    $("#product_name").val(name);
    $("#product_sku").val(sku);
    $("#product_category_id").val(category_id);
    $("#product_brand").val(brand);
    $("#product_description").val(description);
    new bootstrap.Modal($("#upsertProductModal")).show();
}
</script>
