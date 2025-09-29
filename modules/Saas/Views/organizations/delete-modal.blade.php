<div class="modal fade" id="deleteOrganizationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Delete Organization</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteOrganizationForm">
                @csrf @method('delete')
                <div class="modal-body">
                    <p>Are you sure you want to delete this organization?</p>
                    <input type="hidden" id="delete_organization_id">
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
        $("#delete_organization_id").val(id);
        new bootstrap.Modal($("#deleteOrganizationModal")).show();
    }

    $("#deleteOrganizationForm").on("submit", function(e) {
        e.preventDefault();
        let id = $("#delete_organization_id").val();

        $.ajax({
            url: "{{ route('saas.organizations.delete', ':id') }}".replace(':id', id),
            type: "DELETE",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            success: function(data) {
                $(`#organization_${id}`).remove();
                organizations = organizations.filter(o => o.id !== parseInt(id));
                bootstrap.Modal.getInstance($("#deleteOrganizationModal")[0]).hide();

                showToast(data.message, "success");
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || "Delete failed", "error");
            }
        });
    });

</script>
