@extends('layouts.app')

@section('title', 'Organizations')

@section('content')
<div class="mobile-padding">

    <!-- Search + Add -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input type="text" id="organizationSearch" class="form-control rounded-pill" placeholder="Search organizations...">
        @include('saas::organizations.upsert-modal')
    </div>

    <!-- Organizations Grid -->
    <div id="organizations-container" class="dls-container"></div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
</div>

@include('saas::organizations.delete-modal')
@endsection

@push('scripts')
<script>
    let organizations = @json($organizations ?? []);
    let currentPage = 1;
    const perPage = 12;

    function organizationCard(o) {
        return `
        <div class="dl-card" id="organization_${o.id}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h6><i class="bi bi-building"></i> ${o.name}</h6>
                    <small class="text-muted">${o.slug ?? ''}</small><br>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-warning rounded-pill"
                        onclick="openEdit(${o.id}, '${o.name}', '${o.slug ?? ''}', '${o.email ?? ''}', '${o.phone ?? ''}', '${o.address ?? ''}')">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="openDelete(${o.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div>
                <small class="text-muted"><b>Email:</b> ${o.email ?? ''}</small><br>
                <small class="text-muted"><b>Phone:</b> ${o.phone ?? ''}</small><br>
                <small class="text-muted"><b>Address:</b> ${o.address ?? ''}</small>
            </div>
        </div>
        `;
    }

    function renderOrganizations() {
        const container = $("#organizations-container");
        container.html("");
        const start = (currentPage - 1) * perPage;
        const pageItems = organizations.slice(start, start + perPage);

        if (pageItems.length === 0) {
            container.html(`<div class="dl-card">No organizations found</div>`);
            return;
        }
        pageItems.forEach(o => container.append(organizationCard(o)));
    }

    function renderPagination() {
        const totalPages = Math.ceil(organizations.length / perPage);
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
        renderOrganizations();
        renderPagination();
    }

    // ---- Search ----
    $("#organizationSearch").on("input", function() {
        const q = this.value.toLowerCase();
        organizations = @json($organizations ?? []).filter(o =>
            (o.name ?? '').toLowerCase().includes(q) ||
            (o.email ?? '').toLowerCase().includes(q) ||
            (o.phone ?? '').toLowerCase().includes(q)
        );
        currentPage = 1;
        renderOrganizations();
        renderPagination();
    });

    // Init
    renderOrganizations();
    renderPagination();
</script>
@endpush
