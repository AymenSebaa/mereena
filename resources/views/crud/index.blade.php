@extends('layouts.app')

@section('title', $items ?? 'Items')

@section('content')
<div class="mobile-padding">

    <!-- Search + Add -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input type="text" id="crudSearch" class="form-control rounded-pill" placeholder="Search {{ strtolower($items) }}...">
        @include('crud.upsert-modal')
    </div>

    <!-- Grid -->
    <div id="crud-container" class="dls-container"></div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
</div>

@include('crud.delete-modal')
@endsection

@push('scripts')
<script>
    let items = [];
    let currentPage = 1;
    const perPage = 12;
    const entity = "{{ $item }}"; // e.g. "continents", "regions"

    // ---- Card Template (override-able) ----
    function itemCard(item) {
        return `
        <div class="dl-card" id="${entity}_${item.id}">
            <div class="d-flex justify-content-between align-items-center">
                @yield('item-header')
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-warning rounded-pill" onclick="openEdit(${item.id})">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="openDelete(${item.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            @yield('item-body')
        </div>
        `;
    }

    function renderItems() {
        const container = $("#crud-container");
        container.html("");
        const start = (currentPage - 1) * perPage;
        const pageItems = items.slice(start, start + perPage);

        if (pageItems.length === 0) {
            container.html(`<div class="dl-card">No ${entity} found</div>`);
            return;
        }
        pageItems.forEach(i => container.append(itemCard(i)));
    }

    function renderPagination() {
        const totalPages = Math.ceil(items.length / perPage);
        const pagination = $("#pagination");
        pagination.html("");
        if (totalPages <= 1) return;
        for (let i = 1; i <= totalPages; i++) {
            pagination.append(`
                <button class="btn btn-sm rounded-pill ${i === currentPage ? 'btn-primary' : 'btn-light'} mx-1"
                    onclick="goToPage(${i})">${i}</button>
            `);
        }
    }

    function goToPage(page) {
        currentPage = page;
        renderItems();
        renderPagination();
    }

    // ---- Fetch Items from API ----
    async function fetchItems() {
        try {
            let res = await fetch("{{ oRoute($routePrefix.'.index') }}", {
                headers: { "Accept": "application/json" }
            });
            items = await res.json();
            renderItems();
            renderPagination();
        } catch (e) {
            console.error("Failed to fetch", e);
        }
    }

    // ---- Search ----
    $("#crudSearch").on("input", async function() {
        const q = this.value.toLowerCase();
        try {
            let res = await fetch("{{ oRoute($routePrefix.'.search') }}?q=" + encodeURIComponent(q), {
                headers: { "Accept": "application/json" }
            });
            items = await res.json();
            currentPage = 1;
            renderItems();
            renderPagination();
        } catch (e) {
            console.error("Search failed", e);
        }
    });

    // Init
    fetchItems();
</script>
@endpush
