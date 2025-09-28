@extends('layouts.app')

@section('title', 'States')

@section('content')
<div class="mobile-padding">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input type="text" id="stateSearch" class="form-control rounded-pill" placeholder="Search states...">
        @include('world::states.upsert-modal')
    </div>

    <div id="states-container" class="dls-container"></div>
    <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
</div>

@include('world::states.delete-modal')
@endsection

@push('scripts')
<script>
    let states = @json($states ?? []);
    let currentPage = 1;
    const perPage = 24;

    function stateCard(s) {
        return `
        <div class="dl-card" id="state_${s.id}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h6><i class="bi bi-geo-alt"></i> ${s.name}</h6>
                    <small class="text-muted">
                        ${s.country?.name ?? 'N/A'} | ${s.country?.region?.name ?? 'N/A'} | ${s.country?.region?.continent?.name ?? 'N/A'}
                    </small>
                    <div class="mt-1">
                        <span class="badge bg-secondary">ISO2: ${s.iso2 || 'N/A'}</span> 
                        <span class="badge bg-muted">Lat: ${s.lat ?? 'N/A'} | Lng: ${s.lng ?? 'N/A'}</span>
                    </div>
                </div>
                <div class="mt-2 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-warning rounded-pill"
                        onclick="openEdit(${s.id}, '${s.name}', '${s.iso2 ?? ''}', ${s.lat ?? 'null'}, ${s.lng ?? 'null'}, ${s.country_id})">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="openDelete(${s.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        `;
    }

    function renderStates() {
        const container = $("#states-container");
        container.html("");
        const start = (currentPage - 1) * perPage;
        const pageItems = states.slice(start, start + perPage);

        if (!pageItems.length) {
            container.html(`<div class="dl-card">No states found</div>`);
            return;
        }
        pageItems.forEach(s => container.append(stateCard(s)));
    }

    function renderPagination() {
        const totalPages = Math.ceil(states.length / perPage);
        const pagination = $("#pagination");
        pagination.html("");
        if (totalPages <= 1) return;
        for (let i = 1; i <= totalPages; i++) {
            pagination.append(`<button class="btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-light'} mx-1" onclick="goToPage(${i})">${i}</button>`);
        }
    }

    function goToPage(page) {
        currentPage = page;
        renderStates();
        renderPagination();
    }

    // Search
    $("#stateSearch").on("input", function() {
        const q = this.value.toLowerCase();
        states = @json($states ?? []).filter(s => (s.name ?? '').toLowerCase().includes(q));
        currentPage = 1;
        renderStates();
        renderPagination();
    });

    // Init
    renderStates();
    renderPagination();
</script>
@endpush
