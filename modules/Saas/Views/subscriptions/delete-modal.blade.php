<div class="modal fade" id="deleteSubscriptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Delete Subscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteSubscriptionForm">
                @csrf @method('delete')
                <div class="modal-body">
                    <p>Are you sure you want to delete this subscription?</p>
                    <input type="hidden" id="delete_subscription_id">
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
        $("#delete_subscription_id").val(id);
        new bootstrap.Modal($("#deleteSubscriptionModal")).show();
    }

    $("#deleteSubscriptionForm").on("submit", function(e) {
        e.preventDefault();
        let id = $("#delete_subscription_id").val();

        $.ajax({
            url: "{{ route('saas.subscriptions.delete', ':id') }}".replace(':id', id),
            type: "DELETE",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            success: function(data) {
                $(`#subscription_${id}`).remove();
                subscriptions = subscriptions.filter(s => s.id !== parseInt(id));
                bootstrap.Modal.getInstance($("#deleteSubscriptionModal")[0]).hide();
                
                showToast(data.message, "success");
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || "Delete failed", "error");
            }
        });
    });
</script>
