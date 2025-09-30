<div class="modal fade" id="crudDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Delete {{ ucfirst($item ?? 'Item') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="crudDeleteForm">
                @csrf @method('delete')
                <div class="modal-body">
                    <p>Are you sure you want to delete this {{ strtolower($item ?? 'item') }}?</p>
                    <input type="hidden" id="crud_delete_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openDelete(id) {
        $("#crud_delete_id").val(id);
        new bootstrap.Modal($("#crudDeleteModal")).show();
    }

    $("#crudDeleteForm").on("submit", async function(e) {
        e.preventDefault();
        let id = $("#crud_delete_id").val();

        try {
            await $.ajax({
                url: "{{ oRoute($routePrefix.'.delete', ':id') }}".replace(':id', id),
                type: "DELETE",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
            });
            $(`#${entity}_${id}`).remove();
            bootstrap.Modal.getInstance($("#crudDeleteModal")[0]).hide();
        } catch (xhr) {
            console.error("Delete failed:", xhr.responseJSON?.message || xhr.statusText);
        }
    });
</script>
@endpush
