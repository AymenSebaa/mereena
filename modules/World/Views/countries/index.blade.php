@extends('layouts.app')

@section('title', 'Countries')

@section('content')
<div class="mobile-padding">

    <!-- Search + Add -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input type="text" id="countrySearch" class="form-control rounded-pill" placeholder="Search countries...">
        @include('world::countries.upsert-modal')
    </div>

    <!-- Countries Grid -->
    <div id="countries-container" class="dls-container"></div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
</div>

@include('world::countries.delete-modal')
@endsection

@push('scripts')
<script>
    let countries = @json($countries ?? []);
    let currentPage = 1;
    const perPage = 24;

   function countryCard(c) {
        return `
        <div class="dl-card" id="country_${c.id}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h6 class="d-flex align-items-center">
                        <i class="bi bi-flag"></i> 
                        <span class='ms-2' >${c.name}</span> 
                        <span class='display-6 ms-2' >${c.emoji || ''}</span>
                    </h6>
                    <small class="text-muted">
                        Region: ${c.region?.name ?? 'N/A'} | Continent: ${c.region?.continent?.name ?? 'N/A'}
                    </small>
                    <div class="mt-1">
                        <span class="badge bg-secondary">ISO2: ${c.iso2}</span>
                        <span class="badge bg-secondary">ISO3: ${c.iso3}</span>
                        <span class="badge bg-info text-dark">Phone: ${c.phone_code || 'N/A'}</span>
                        <span class="badge bg-success">Currency: ${c.currency || 'N/A'}</span>
                    </div>
                    <div class="mt-1 text-muted">
                        Lat: ${c.lat ?? 'N/A'}, Lng: ${c.lng ?? 'N/A'}
                    </div>
                </div>
                <div class="mt-2 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-warning rounded-pill"
                        onclick="openEdit(${c.id}, '${c.name}', '${c.iso2}', '${c.iso3}', '${c.phone_code ?? ''}', '${c.currency ?? ''}', '${c.emoji ?? ''}', ${c.lat ?? 'null'}, ${c.lng ?? 'null'}, ${c.region_id})">
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


    function renderCountries() {
        const container = $("#countries-container");
        container.html("");
        const start = (currentPage - 1) * perPage;
        const pageItems = countries.slice(start, start + perPage);

        if (pageItems.length === 0) {
            container.html(`<div class="dl-card">No countries found</div>`);
            return;
        }
        pageItems.forEach(c => container.append(countryCard(c)));
    }

    function renderPagination() {
        const totalPages = Math.ceil(countries.length / perPage);
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
        renderCountries();
        renderPagination();
    }

    $("#countrySearch").on("input", function() {
        const q = this.value.toLowerCase();
        countries = @json($countries ?? []).filter(c =>
            (c.name ?? '').toLowerCase().includes(q)
        );
        currentPage = 1;
        renderCountries();
        renderPagination();
    });

    // Init
    renderCountries();
    renderPagination();
</script>
@endpush
