<!-- Delete Modal -->
<div class="modal fade" id="deleteSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Delete Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteSupplierForm">
                @csrf @method('delete')
                <div class="modal-body">
                    <p>Are you sure you want to delete this supplier?</p>
                    <input type="hidden" id="delete_supplier_id">
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
    function openDelete(id) {
        document.getElementById('delete_supplier_id').value = id;
        new bootstrap.Modal(document.getElementById('deleteSupplierModal')).show();
    }

    document.getElementById('deleteSupplierForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let id = document.getElementById('delete_supplier_id').value;

        fetch("{{ route('stock.suppliers.delete', ':id') }}".replace(':id', id), {
                method: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.result) {
                    document.getElementById(`supplier_${id}`).remove();
                    suppliers = suppliers.filter(s => s.id !== parseInt(id));
                    bootstrap.Modal.getInstance(document.getElementById('deleteSupplierModal')).hide();
                }
            })
            .catch(err => console.error(err));
    });
</script>
