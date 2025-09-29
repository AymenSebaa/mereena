@extends('layouts.app')

@section('title', 'Plans')

@section('content')
<div class="mobile-padding">

    <!-- Search + Add -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input type="text" id="planSearch" class="form-control rounded-pill" placeholder="Search plans...">
        @include('saas::plans.upsert-modal')
    </div>

    <!-- Plans Grid -->
    <div id="plans-container" class="dls-container"></div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
</div>

@include('saas::plans.delete-modal')
@endsection

@push('scripts')
<script>
    let plans = @json($plans ?? []);
    let currentPage = 1;
    const perPage = 12;

    function planCard(p) {
        return `
        <div class="dl-card" id="plan_${p.id}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6><i class="bi bi-tags"></i> ${p.name}</h6>
                <div class="small text-muted">${p.interval} — $${p.price}</div>
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-warning rounded-pill"
                        onclick="openEdit(${p.id}, '${p.name}', '${p.slug}', '${p.price}', '${p.interval}')">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="openDelete(${p.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        `;
    }

    function renderPlans() {
        const container = $("#plans-container");
        container.html("");
        const start = (currentPage - 1) * perPage;
        const pageItems = plans.slice(start, start + perPage);

        if (pageItems.length === 0) {
            container.html(`<div class="dl-card">No plans found</div>`);
            return;
        }
        pageItems.forEach(p => container.append(planCard(p)));
    }

    function renderPagination() {
        const totalPages = Math.ceil(plans.length / perPage);
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
        renderPlans();
        renderPagination();
    }

    // ---- Search ----
    $("#planSearch").on("input", function() {
        const q = this.value.toLowerCase();
        plans = @json($plans ?? []).filter(p =>
            (p.name ?? '').toLowerCase().includes(q) ||
            (p.slug ?? '').toLowerCase().includes(q)
        );
        currentPage = 1;
        renderPlans();
        renderPagination();
    });

    // Init
    renderPlans();
    renderPagination();
</script>
@endpush
