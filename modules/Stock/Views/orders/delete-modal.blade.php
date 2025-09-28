<div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="deleteOrderForm">
            @csrf
            @method('DELETE')
            <input type="hidden" id="delete_order_id">

            <div class="modal-content glass-card">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this order?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openDeleteOrder(id) {
        $("#delete_order_id").val(id);
        new bootstrap.Modal($("#deleteOrderModal")).show();
    }

    $("#deleteOrderForm").on("submit", function(e) {
        e.preventDefault();
        let id = $("#delete_order_id").val();

        $.ajax({
            url: "{{ route('stock.orders.delete', ':id') }}".replace(':id', id),
            type: "DELETE",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            success: function(data) {
                $(`#order_${id}`).remove();
                bootstrap.Modal.getInstance($("#deleteOrderModal")[0]).hide();
            },
            error: function(xhr) {
                console.error("Delete failed:", xhr.responseJSON?.message || xhr.statusText);
            }
        });
    });
</script>
