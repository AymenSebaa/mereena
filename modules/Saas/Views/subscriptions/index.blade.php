@extends('layouts.app')

@section('title', 'Subscriptions')

@section('content')
<div class="mobile-padding">

    <!-- Search + Add -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input type="text" id="subscriptionSearch" class="form-control rounded-pill" placeholder="Search subscriptions...">
        @include('saas::subscriptions.upsert-modal')
    </div>

    <!-- Subscriptions Grid -->
    <div id="subscriptions-container" class="dls-container"></div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
</div>

@include('saas::subscriptions.delete-modal')
@endsection

@push('scripts')
<script>
    let subscriptions = @json($subscriptions ?? []);
    let currentPage = 1;
    const perPage = 12;

    function subscriptionCard(s) {
        return `
        <div class="dl-card" id="subscription_${s.id}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6><i class="bi bi-file-earmark-text"></i> ${s.organization?.name ?? ''}</h6>
                <small class="text-muted">${s.plan?.name ?? ''}</small>
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-warning rounded-pill"
                        onclick="openEdit(${s.id}, ${s.organization_id}, ${s.plan_id}, '${s.status}', '${s.starts_at}', '${s.ends_at}')">
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

    function renderSubscriptions() {
        const container = $("#subscriptions-container");
        container.html("");
        const start = (currentPage - 1) * perPage;
        const pageItems = subscriptions.slice(start, start + perPage);

        if (pageItems.length === 0) {
            container.html(`<div class="dl-card">No subscriptions found</div>`);
            return;
        }
        pageItems.forEach(s => container.append(subscriptionCard(s)));
    }

    function renderPagination() {
        const totalPages = Math.ceil(subscriptions.length / perPage);
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
        renderSubscriptions();
        renderPagination();
    }

    // ---- Search ----
    $("#subscriptionSearch").on("input", function() {
        const q = this.value.toLowerCase();
        subscriptions = @json($subscriptions ?? []).filter(s =>
            (s.organization?.name ?? '').toLowerCase().includes(q) ||
            (s.plan?.name ?? '').toLowerCase().includes(q) ||
            (s.status ?? '').toLowerCase().includes(q)
        );
        currentPage = 1;
        renderSubscriptions();
        renderPagination();
    });

    // Init
    renderSubscriptions();
    renderPagination();
</script>
@endpush
