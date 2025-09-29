<!-- Delete Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Delete Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteProductForm">
                @csrf @method('delete')
                <div class="modal-body">
                    <p>Are you sure you want to delete this product?</p>
                    <input type="hidden" id="delete_product_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openDelete(id) {
        $("#delete_product_id").val(id);
        new bootstrap.Modal($("#deleteProductModal")).show();
    }

    $("#deleteProductForm").on("submit", function(e) {
        e.preventDefault();
        let id = $("#delete_product_id").val();

        $.ajax({
            url: "{{ oRoute('stock.products.delete', ':id') }}".replace(':id', id),
            type: "DELETE",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            success: function(data) {
                if (data.result) {
                    $(`#product_${id}`).remove();
                    products = products.filter(p => p.id !== parseInt(id));
                    bootstrap.Modal.getInstance($("#deleteProductModal")[0]).hide();
                }
            },
            error: function(xhr) {
                console.error("Delete failed:", xhr.responseJSON?.message || xhr.statusText);
            }
        });
    });
</script>
