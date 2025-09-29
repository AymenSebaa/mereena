<!-- Delete Modal -->
<div class="modal fade" id="deleteSiteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Delete Site</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteSiteForm">
                @csrf @method('delete')
                <div class="modal-body">
                    <p>Are you sure you want to delete this site?</p>
                    <input type="hidden" id="delete_site_id">
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
    // ---- Delete ----
    function openDeleteSite(id) {
        document.getElementById('delete_site_id').value = id;
        new bootstrap.Modal(document.getElementById('deleteSiteModal')).show();
    }

    document.getElementById('deleteSiteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let id = document.getElementById('delete_site_id').value;

        fetch("{{ route('sites.delete', ':id') }}".replace(':id', id), {
                method: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (res.result) {
                    fetchSites();
                    bootstrap.Modal.getInstance(document.getElementById('deleteSiteModal')).hide();
                    showToast(res.message, 'success');
                }
            })
            .catch(err => console.error(err));
    });
</script>
