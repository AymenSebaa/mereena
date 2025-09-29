<button class="btn btn-primary rounded-pill w-50 m-3" data-bs-toggle="modal" data-bs-target="#upsertSupplierModal">
    <i class="bi bi-plus-circle me-2"></i> New
</button>

<!-- Upsert Modal -->
<div class="modal fade" id="upsertSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Upsert Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="upsertSupplierForm">
                @csrf
                <input type="hidden" name="id" id="supplier_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control rounded-pill" name="name" id="supplier_name" required>
                    </div>
                    <div class="mb-3">
                        <label>Contact</label>
                        <input type="text" class="form-control rounded-pill" name="contact" id="supplier_contact">
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control rounded-pill" name="email" id="supplier_email">
                    </div>
                    <div class="mb-3">
                        <label>Phone</label>
                        <input type="text" class="form-control rounded-pill" name="phone" id="supplier_phone">
                    </div>
                    <div class="mb-3">
                        <label>Address</label>
                        <input type="text" class="form-control rounded-pill" name="address" id="supplier_address">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark rounded-pill" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success rounded-pill">Save Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ---- Upsert ----
    document.getElementById('upsertSupplierForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch("{{ oRoute('stock.suppliers.upsert') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.result) {
                    let existing = document.getElementById(`supplier_${data.data.id}`);
                    if (existing) {
                        existing.outerHTML = supplierCard(data.data); // update
                    } else {
                        document.getElementById('suppliers-container')
                            .insertAdjacentHTML('afterbegin', supplierCard(data.data)); // prepend new
                        suppliers.unshift(data.data);
                    }
                    bootstrap.Modal.getInstance(document.getElementById('upsertSupplierModal')).hide();
                }
            })
            .catch(err => console.error(err));
    });

    // ---- Edit ----
    function openEdit(id, name, contact, email, phone, address) {
        document.getElementById('supplier_id').value = id;
        document.getElementById('supplier_name').value = name;
        document.getElementById('supplier_contact').value = contact;
        document.getElementById('supplier_email').value = email;
        document.getElementById('supplier_phone').value = phone;
        document.getElementById('supplier_address').value = address;
        new bootstrap.Modal(document.getElementById('upsertSupplierModal')).show();
    }
</script>
