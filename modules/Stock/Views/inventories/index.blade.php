@extends('layouts.app')

@section('title', 'Inventories')

@section('content')
    <div class="mobile-padding">

        <!-- Search + Add -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <input type="text" id="inventorySearch" class="form-control rounded-pill" placeholder="Search inventories...">
            @include('stock::inventories.upsert-modal')
        </div>

        <!-- Inventories Grid -->
        <div id="inventories-container" class="dls-container"></div>

        <!-- Pagination -->
        <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
    </div>

    @include('stock::inventories.delete-modal')
@endsection

@push('scripts')
    <script>
        let inventories = @json($inventories ?? []);
        let currentPage = 1;
        const perPage = 12;

        function inventoryCard(inv) {
            return `
            <div class="dl-card" id="inventory_${inv.id}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6><i class="bi bi-box"></i> ${inv.product?.name ?? '-'}</h6>
                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-sm btn-outline-warning rounded-pill"
                            onclick="openEdit(${inv.id}, '${inv.product_id}', '${inv.supplier_id}', '${inv.price}', '${inv.quantity}', '${inv.made_at ?? ''}', '${inv.expires_at ?? ''}')">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="openDelete(${inv.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="small mb-2">
                    <i class="bi bi-truck"></i>  ${inv.supplier?.name ?? '-'} <br>
                    <i class="bi bi-cash"></i> ${inv.price} | <i class="bi bi-boxes"></i> ${inv.quantity}<br>
                    <i class="bi bi-calendar-plus"></i> ${inv.made_at ?? '-'} | <i class="bi bi-calendar-x"></i> ${inv.expires_at ?? '-'}
                </div>
            </div>
        `;
        }

        function renderInventories() {
            const container = $("#inventories-container");
            container.html("");
            const start = (currentPage - 1) * perPage;
            const pageItems = inventories.slice(start, start + perPage);

            if (pageItems.length === 0) {
                container.html(`<div class="dl-card">No inventories found</div>`);
                return;
            }
            pageItems.forEach(inv => container.append(inventoryCard(inv)));
        }

        function renderPagination() {
            const totalPages = Math.ceil(inventories.length / perPage);
            const pagination = $("#pagination");
            pagination.html("");
            if (totalPages <= 1) return;
            for (let i = 1; i <= totalPages; i++) {
                pagination.append(`
                <button class="btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-light'} mx-1"
                    onclick="goToPage(${i})">${i}</button>
            `);
            }
        }

        function goToPage(page) {
            currentPage = page;
            renderInventories();
            renderPagination();
        }

        // ---- Search ----
        $("#inventorySearch").on("input", function() {
            const q = this.value.toLowerCase();
            inventories = @json($inventories ?? []).filter(inv =>
                (inv.product?.name ?? '').toLowerCase().includes(q) ||
                (inv.supplier?.name ?? '').toLowerCase().includes(q)
            );
            currentPage = 1;
            renderInventories();
            renderPagination();
        });

        // Init
        renderInventories();
        renderPagination();
    </script>
@endpush
