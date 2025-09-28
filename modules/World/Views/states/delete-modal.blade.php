<div class="modal fade" id="deleteStateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Delete State</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteStateForm">
                @csrf @method('delete')
                <div class="modal-body">
                    <p>Are you sure you want to delete this state?</p>
                    <input type="hidden" id="delete_state_id">
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
        $("#delete_state_id").val(id);
        new bootstrap.Modal($("#deleteStateModal")).show();
    }

    $("#deleteStateForm").on("submit", function(e) {
        e.preventDefault();
        let id = $("#delete_state_id").val();

        $.ajax({
            url: "{{ route('world.states.delete', ':id') }}".replace(':id', id),
            type: "DELETE",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            success: function(data) {
                $(`#state_${id}`).remove();
                states = states.filter(s => s.id !== parseInt(id));
                bootstrap.Modal.getInstance($("#deleteStateModal")[0]).hide();
                renderPagination();
            },
            error: function(xhr) {
                console.error("Delete failed:", xhr.responseJSON?.message || xhr.statusText);
            }
        });
    });
</script>