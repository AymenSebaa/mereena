@extends('layouts.app')

@section('title', 'Cities')

@section('content')
<div class="mobile-padding">

    <!-- Search + Add -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input type="text" id="citySearch" class="form-control rounded-pill" placeholder="Search cities...">
        @include('world::cities.upsert-modal')
    </div>

    <!-- Cities Grid -->
    <div id="cities-container" class="dls-container"></div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
</div>

@include('world::cities.delete-modal')
@endsection

@push('scripts')
<script>
    let cities = @json($cities ?? []);
    let currentPage = 1;
    const perPage = 24;

    function cityCard(c) {
        return `
        <div class="dl-card" id="city_${c.id}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6>
                    <i class="bi bi-pin-map"></i> ${c.name}
                </h6>
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-warning rounded-pill"
                        onclick="openEdit(${c.id}, '${c.name}', '${c.zip_code ?? ''}', ${c.lat ?? 'null'}, ${c.lng ?? 'null'}, ${c.state_id})">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="openDelete(${c.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <small class="text-muted">
                (${c.state?.name ?? 'N/A'} | ${c.state?.country?.name ?? 'N/A'})
            </small>
        </div>
        `;
    }

    function renderCities() {
        const container = $("#cities-container");
        container.html("");
        const start = (currentPage - 1) * perPage;
        const pageItems = cities.slice(start, start + perPage);

        if (pageItems.length === 0) {
            container.html(`<div class="dl-card">No cities found</div>`);
            return;
        }
        pageItems.forEach(c => container.append(cityCard(c)));
    }

    function renderPagination() {
        const totalPages = Math.ceil(cities.length / perPage);
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
        renderCities();
        renderPagination();
    }

    // ---- Search ----
    $("#citySearch").on("input", function() {
        const q = this.value.toLowerCase();
        cities = @json($cities ?? []).filter(c =>
            (c.name ?? '').toLowerCase().includes(q)
        );
        currentPage = 1;
        renderCities();
        renderPagination();
    });

    // Init
    renderCities();
    renderPagination();

</script>
@endpush