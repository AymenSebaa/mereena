@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
    <div class="mobile-padding">

        <!-- Search + Add -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <input type="text" id="supplierSearch" class="form-control rounded-pill" placeholder="Search suppliers...">
            @include('stock::suppliers.upsert-modal')
        </div>

        <!-- Suppliers Grid -->
        <div id="suppliers-container" class="dls-container"></div>

        <!-- Pagination -->
        <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
    </div>

    @include('stock::suppliers.delete-modal')
@endsection

@push('scripts')
    <script>
        let suppliers = @json($suppliers ?? []);
        let currentPage = 1;
        const perPage = 12;

        function supplierCard(supplier) {
            return `
            <div class="dl-card" id="supplier_${supplier.id}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6>${supplier.name}</h6>
                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-sm btn-outline-warning rounded-pill"
                            onclick="openEdit(${supplier.id}, '${supplier.name}', '${supplier.contact ?? ''}', '${supplier.email ?? ''}', '${supplier.phone ?? ''}', '${supplier.address ?? ''}')">
                            <i class="bi bi-pencil-square"></i> 
                        </button>
                        <button class="btn btn-sm btn-outline-danger rounded-pill"
                            onclick="openDelete(${supplier.id})">
                            <i class="bi bi-trash"></i> 
                        </button>
                    </div>
                </div>
                <div class="small mb-2">
                    <i class="bi bi-person-lines-fill"></i> ${supplier.contact ?? '-'}<br>
                    <i class="bi bi-envelope"></i> ${supplier.email ?? '-'}<br>
                    <i class="bi bi-telephone"></i> ${supplier.phone ?? '-'}<br>
                    <i class="bi bi-geo-alt"></i> ${supplier.address ?? '-'}
                </div>
            </div>`;
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
