@extends('layouts.app')

@section('title', 'Continents')

@section('content')
<div class="mobile-padding">

    <!-- Search + Add -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input type="text" id="continentSearch" class="form-control rounded-pill" placeholder="Search continents...">
        @include('world::continents.upsert-modal')
    </div>

    <!-- Continents Grid -->
    <div id="continents-container" class="dls-container"></div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
</div>

@include('world::continents.delete-modal')
@endsection

@push('scripts')
<script>
    let continents = @json($continents ?? []);
    let currentPage = 1;
    const perPage = 12;

    function continentCard(c) {
        return `
        <div class="dl-card" id="continent_${c.id}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6><i class="bi bi-globe-americas"></i> ${c.name}</h6>
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-warning rounded-pill"
                        onclick="openEdit(${c.id}, '${c.name}')">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="openDelete(${c.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        `;
    }

    function renderContinents() {
        const container = $("#continents-container");
        container.html("");
        const start = (currentPage - 1) * perPage;
        const pageItems = continents.slice(start, start + perPage);

        if (pageItems.length === 0) {
            container.html(`<div class="dl-card">No continents found</div>`);
            return;
        }
        pageItems.forEach(c => container.append(continentCard(c)));
    }

    function renderPagination() {
        const totalPages = Math.ceil(continents.length / perPage);
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
        renderContinents();
        renderPagination();
    }

    // ---- Search ----
    $("#continentSearch").on("input", function() {
        const q = this.value.toLowerCase();
        continents = @json($continents ?? []).filter(c =>
            (c.name ?? '').toLowerCase().includes(q)
        );
        currentPage = 1;
        renderContinents();
        renderPagination();
    });

    // Init
    renderContinents();
    renderPagination();
</script>
@endpush
