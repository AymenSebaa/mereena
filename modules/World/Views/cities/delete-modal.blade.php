<div class="modal fade" id="deleteCityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Delete City</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteCityForm">
                @csrf @method('delete')
                <div class="modal-body">
                    <p>Are you sure you want to delete this city?</p>
                    <input type="hidden" id="delete_city_id">
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
        $("#delete_city_id").val(id);
        new bootstrap.Modal($("#deleteCityModal")).show();
    }

    $("#deleteCityForm").on("submit", function(e) {
        e.preventDefault();
        let id = $("#delete_city_id").val();

        $.ajax({
            url: "{{ oRoute('world.cities.delete', ':id') }}".replace(':id', id),
            type: "DELETE",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            success: function(data) {
                $(`#city_${id}`).remove();
                cities = cities.filter(c => c.id !== parseInt(id));
                bootstrap.Modal.getInstance($("#deleteCityModal")[0]).hide();
            },
            error: function(xhr) {
                console.error("Delete failed:", xhr.responseJSON?.message || xhr.statusText);
            }
        });
    });

</script>