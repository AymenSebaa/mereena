@extends('layouts.app')

@section('title', 'Organization Users')

@section('content')
<div class="mobile-padding">

    <!-- Search + Add -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input type="text" id="orgUserSearch" class="form-control rounded-pill" placeholder="Search organization users...">
        @include('saas::organization_users.upsert-modal')
    </div>

    <!-- Organization Users Grid -->
    <div id="org-users-container" class="dls-container"></div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
</div>

@include('saas::organization_users.delete-modal')
@endsection

@push('scripts')
<script>
    let orgUsers = @json($orgUsers ?? []);
    let currentPage = 1;
    const perPage = 12;

    function orgUserCard(u) {
        return `
        <div class="dl-card" id="org_user_${u.id}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6><i class="bi bi-person-badge"></i> ${u.user?.name ?? "Unknown User"}</h6>
                <small class="text-muted">${u.organization?.name ?? "Unknown Org"}</small>
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-warning rounded-pill"
                        onclick="openEdit(${u.id}, '${u.organization_id}', '${u.user_id}', '${u.role}')">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="openDelete(${u.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="text-muted">Role: ${u.role}</div>
        </div>
        `;
    }

    function renderOrgUsers() {
        const container = $("#org-users-container");
        container.html("");
        const start = (currentPage - 1) * perPage;
        const pageItems = orgUsers.slice(start, start + perPage);

        if (pageItems.length === 0) {
            container.html(`<div class="dl-card">No organization users found</div>`);
            return;
        }
        pageItems.forEach(u => container.append(orgUserCard(u)));
    }

    function renderPagination() {
        const totalPages = Math.ceil(orgUsers.length / perPage);
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
        renderOrgUsers();
        renderPagination();
    }

    // ---- Search ----
    $("#orgUserSearch").on("input", function() {
        const q = this.value.toLowerCase();
        orgUsers = @json($orgUsers ?? []).filter(u =>
            (u.user?.name ?? '').toLowerCase().includes(q) ||
            (u.organization?.name ?? '').toLowerCase().includes(q) ||
            (u.role ?? '').toLowerCase().includes(q)
        );
        currentPage = 1;
        renderOrgUsers();
        renderPagination();
    });

    // Init
    renderOrgUsers();
    renderPagination();
</script>
@endpush
