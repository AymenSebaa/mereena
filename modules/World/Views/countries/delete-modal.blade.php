<div class="modal fade" id="deleteCountryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Delete Country</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteCountryForm">
                @csrf @method('delete')
                <div class="modal-body">
                    <p>Are you sure you want to delete this country?</p>
                    <input type="hidden" id="delete_country_id">
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
        $("#delete_country_id").val(id);
        new bootstrap.Modal($("#deleteCountryModal")).show();
    }

    $("#deleteCountryForm").on("submit", function(e) {
        e.preventDefault();
        let id = $("#delete_country_id").val();

        $.ajax({
            url: "{{ oRoute('world.countries.delete', ':id') }}".replace(':id', id),
            type: "DELETE",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            success: function(data) {
                $(`#country_${id}`).remove();
                countries = countries.filter(c => c.id !== parseInt(id));
                bootstrap.Modal.getInstance($("#deleteCountryModal")[0]).hide();
            },
            error: function(xhr) {
                console.error("Delete failed:", xhr.responseJSON?.message || xhr.statusText);
            }
        });
    });
</script>
