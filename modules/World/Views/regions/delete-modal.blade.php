<div class="modal fade" id="deleteRegionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Delete Region</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteRegionForm">
                @csrf @method('delete')
                <div class="modal-body">
                    <p>Are you sure you want to delete this region?</p>
                    <input type="hidden" id="delete_region_id">
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
        $("#delete_region_id").val(id);
        new bootstrap.Modal($("#deleteRegionModal")).show();
    }

    $("#deleteRegionForm").on("submit", function(e) {
        e.preventDefault();
        let id = $("#delete_region_id").val();

        $.ajax({
            url: "{{ oRoute('world.regions.delete', ':id') }}".replace(':id', id),
            type: "DELETE",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            success: function(data) {
                $(`#region_${id}`).remove();
                regions = regions.filter(r => r.id !== parseInt(id));
                bootstrap.Modal.getInstance($("#deleteRegionModal")[0]).hide();
            },
            error: function(xhr) {
                console.error("Delete failed:", xhr.responseJSON?.message || xhr.statusText);
            }
        });
    });
</script>
