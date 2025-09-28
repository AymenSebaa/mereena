@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
    <div class="mobile-padding">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb glass-card p-3 mb-4">
                <li class="breadcrumb-item active">Suppliers</li>
            </ol>
        </nav>

        <!-- Search + Add -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <input type="text" id="supplierSearch" class="form-control" placeholder="Search suppliers...">
            <button class="btn btn-primary w-50 m-3" data-bs-toggle="modal" data-bs-target="#upsertSupplierModal">
                <i class="bi bi-plus-circle me-2"></i> New
            </button>
        </div>

        <!-- Suppliers Grid -->
        <div id="suppliers-container" class="dls-container"></div>

        <!-- Pagination -->
        <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
    </div>

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
                            <input type="text" class="form-control" name="name" id="supplier_name" required>
                        </div>
                        <div class="mb-3">
                            <label>Contact</label>
                            <input type="text" class="form-control" name="contact" id="supplier_contact">
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" id="supplier_email">
                        </div>
                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" class="form-control" name="phone" id="supplier_phone">
                        </div>
                        <div class="mb-3">
                            <label>Address</label>
                            <input type="text" class="form-control" name="address" id="supplier_address">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
@endsection

@push('scripts')
    <script>
        let suppliers = @json($suppliers ?? []);
        let currentPage = 1;
        const perPage = 12;

        function supplierCard(supplier) {
            return `
            <div class="dl-card" id="supplier_${supplier.id}">
                <div class="d-flex justify-content-between mb-2">
                    <h6>${supplier.name}</h6>
                </div>
                <div class="small mb-2">
                    <i class="bi bi-person-lines-fill"></i> ${supplier.contact ?? '-'}<br>
                    <i class="bi bi-envelope"></i> ${supplier.email ?? '-'}<br>
                    <i class="bi bi-telephone"></i> ${supplier.phone ?? '-'}<br>
                    <i class="bi bi-geo-alt"></i> ${supplier.address ?? '-'}
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-warning"
                        onclick="openEdit(${supplier.id}, '${supplier.name}', '${supplier.contact ?? ''}', '${supplier.email ?? ''}', '${supplier.phone ?? ''}', '${supplier.address ?? ''}')">
                        <i class="bi bi-pencil-square"></i> 
                    </button>
                    <button class="btn btn-sm btn-outline-danger"
                        onclick="openDelete(${supplier.id})">
                        <i class="bi bi-trash"></i> 
                    </button>
                </div>
            </div>
        `;
        }

        function renderSuppliers() {
            const container = document.getElementById('suppliers-container');
            container.innerHTML = '';

            const start = (currentPage - 1) * perPage;
            const pageItems = suppliers.slice(start, start + perPage);

            if (pageItems.length === 0) {
                container.innerHTML = `<div class="dl-card">No suppliers found</div>`;
                return;
            }

            pageItems.forEach(supplier => {
                container.insertAdjacentHTML('beforeend', supplierCard(supplier));
            });
        }

        function renderPagination() {
            const totalPages = Math.ceil(suppliers.length / perPage);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';
            if (totalPages <= 1) return;

            for (let i = 1; i <= totalPages; i++) {
                pagination.insertAdjacentHTML('beforeend', `
                <button class="btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-light'} mx-1"
                    onclick="goToPage(${i})">${i}</button>
            `);
            }
        }

        function goToPage(page) {
            currentPage = page;
            renderSuppliers();
            renderPagination();
        }

        // ---- Upsert ----
        document.getElementById('upsertSupplierForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            fetch("{{ route('suppliers.upsert') }}", {
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

        // ---- Delete ----
        function openDelete(id) {
            document.getElementById('delete_supplier_id').value = id;
            new bootstrap.Modal(document.getElementById('deleteSupplierModal')).show();
        }

        document.getElementById('deleteSupplierForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let id = document.getElementById('delete_supplier_id').value;

            fetch("{{ route('suppliers.delete', ':id') }}".replace(':id', id), {
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

        // ---- Search ----
        document.getElementById('supplierSearch').addEventListener('input', function(e) {
            const q = e.target.value.toLowerCase();
            suppliers = @json($suppliers ?? []).filter(s =>
                (s.name ?? '').toLowerCase().includes(q) ||
                (s.contact ?? '').toLowerCase().includes(q) ||
                (s.email ?? '').toLowerCase().includes(q) ||
                (s.phone ?? '').toLowerCase().includes(q)
            );
            currentPage = 1;
            renderSuppliers();
            renderPagination();
        });

        // Init
        renderSuppliers();
        renderPagination();
    </script>
@endpush
