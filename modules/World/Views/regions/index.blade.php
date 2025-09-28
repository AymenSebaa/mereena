@extends('layouts.app')

@section('title', 'Regions')

@section('content')
<div class="mobile-padding">

    <!-- Search + Add -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input type="text" id="regionSearch" class="form-control rounded-pill" placeholder="Search regions...">
        @include('world::regions.upsert-modal')
    </div>

    <!-- Regions Grid -->
    <div id="regions-container" class="dls-container"></div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
</div>

@include('world::regions.delete-modal')
@endsection

@push('scripts')
<script>
    let regions = @json($regions ?? []);
    let currentPage = 1;
    const perPage = 24;

    function regionCard(r) {
        return `
        <div class="dl-card" id="region_${r.id}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6>
                    <i class="bi bi-geo-alt"></i> ${r.name}
                    <small class="text-muted">(${r.continent?.name ?? 'N/A'})</small>
                </h6>
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-warning rounded-pill"
                        onclick="openEdit(${r.id}, '${r.name}', ${r.m49_code}, ${r.continent_id})">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="openDelete(${r.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        `;
    }

    function renderRegions() {
        const container = $("#regions-container");
        container.html("");
        const start = (currentPage - 1) * perPage;
        const pageItems = regions.slice(start, start + perPage);

        if (pageItems.length === 0) {
            container.html(`<div class="dl-card">No regions found</div>`);
            return;
        }
        pageItems.forEach(r => container.append(regionCard(r)));
    }

    function renderPagination() {
        const totalPages = Math.ceil(regions.length / perPage);
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
        renderRegions();
        renderPagination();
    }

    // ---- Search ----
    $("#regionSearch").on("input", function() {
        const q = this.value.toLowerCase();
        regions = @json($regions ?? []).filter(r =>
            (r.name ?? '').toLowerCase().includes(q)
        );
        currentPage = 1;
        renderRegions();
        renderPagination();
    });

    // Init
    renderRegions();
    renderPagination();
</script>
@endpush
