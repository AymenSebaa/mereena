@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="mobile-padding">

    <!-- Search + Add -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input type="text" id="invoiceSearch" class="form-control rounded-pill" placeholder="Search invoices...">
        @include('saas::invoices.upsert-modal')
    </div>

    <!-- Invoices Grid -->
    <div id="invoices-container" class="dls-container"></div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
</div>

@include('saas::invoices.delete-modal')
@endsection

@push('scripts')
<script>
    let invoices = @json($invoices ?? []);
    let currentPage = 1;
    const perPage = 12;

    function invoiceCard(i) {
        return `
        <div class="dl-card" id="invoice_${i.id}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6><i class="bi bi-receipt"></i> Invoice #${i.id}</h6>
                <span class="badge bg-${i.status === 'paid' ? 'success' : (i.status === 'pending' ? 'warning' : 'secondary')}">${i.status}</span>
            </div>
            <p><strong>Amount:</strong> ${i.amount} DA</p>
            <p><strong>Due:</strong> ${i.due_date ?? '-'}</p>
            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-sm btn-outline-warning rounded-pill"
                    onclick="openEdit(${i.id}, ${i.organization_id}, '${i.amount}', '${i.status}', '${i.due_date}')">
                    <i class="bi bi-pencil-square"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="openDelete(${i.id})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        `;
    }

    function renderInvoices() {
        const container = $("#invoices-container");
        container.html("");
        const start = (currentPage - 1) * perPage;
        const pageItems = invoices.slice(start, start + perPage);

        if (pageItems.length === 0) {
            container.html(`<div class="dl-card">No invoices found</div>`);
            return;
        }
        pageItems.forEach(i => container.append(invoiceCard(i)));
    }

    function renderPagination() {
        const totalPages = Math.ceil(invoices.length / perPage);
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
        renderInvoices();
        renderPagination();
    }

    // ---- Search ----
    $("#invoiceSearch").on("input", function() {
        const q = this.value.toLowerCase();
        invoices = @json($invoices ?? []).filter(i =>
            (String(i.id).toLowerCase().includes(q) || (i.status ?? '').toLowerCase().includes(q))
        );
        currentPage = 1;
        renderInvoices();
        renderPagination();
    });

    // Init
    renderInvoices();
    renderPagination();
</script>
@endpush
