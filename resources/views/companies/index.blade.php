@extends('layouts.app')

@section('title', 'Companies')
@php
    $role_id = auth()->user()->profile->role_id;
@endphp
@section('content')
    <div class="mobile-padding">

        <!-- Global Search + Create -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <input type="text" id="company-search" placeholder="Search companies or buses..." class="form-control w-50">

            @if (false && !in_array($role_id, [3, 4, 10]))
                <button id="create-company-btn" class="btn btn-primary">Add Company</button>
            @endif
        </div>

        <!-- Company Cards -->
        <div id="companies-container" class="companies-container"></div>

        <!-- Pagination -->
        <div id="pagination" class="d-flex justify-content-center mt-4"></div>

        <!-- Upsert Modal -->
        <div class="modal fade" id="upsertCompanyModal" tabindex="-1" aria-labelledby="upsertCompanyModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form id="companyUpsertForm" class="modal-content">
                    @csrf
                    <input type="hidden" name="id" id="company_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="upsertCompanyModalLabel">Edit Company</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="name" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    let allCompanies = [];
    const pageSize = 20;
    let currentPage = 1;

    // Open modal for create
    $('#create-company-btn').on('click', function() {
        $('#company_id').val('');
        $('#company_name').val('');
        $('#upsertCompanyModal').modal('show');
    });

    // Open modal for edit
    $(document).on('dblclick', '.company-card', function() {
        const companyId = $(this).data('id');
        const company = allCompanies.find(c => c.id === companyId);
        if (!company) return;

        $('#company_id').val(company.id);
        $('#company_name').val(company.name);
        $('#upsertCompanyModal').modal('show');
    });

    // Handle submit
    $('#companyUpsertForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.post("{{ route('companies.upsert') }}", formData, function(res) {
            if (res.success) {
                $('#upsertCompanyModal').modal('hide');
                fetchCompanies($('#company-search').val());
            }
        }).fail(function(xhr) {
            alert("Error: " + xhr.responseJSON.message);
        });
    });

    // Render company cards
    function renderPage(page = 1, search = '') {
        currentPage = page;

        // Global search matches company OR any bus name
        const filtered = allCompanies.filter(c =>
            c.name.toLowerCase().includes(search.toLowerCase()) ||
            (c.buses ?? []).some(b => b.name.toLowerCase().includes(search.toLowerCase()))
        );

        const start = (page - 1) * pageSize;
        const end = start + pageSize;
        const pageCompanies = filtered.slice(start, end);

        const container = $('#companies-container');
        container.empty();

        pageCompanies.forEach(company => {
            const busCount = company.buses?.length ?? 0;

            let busList = '';
            if (busCount > 0) {
                busList = `
                    <input type="text" class="form-control form-control-sm mb-2 company-bus-search" 
                        placeholder="Search buses..." data-company-id="${company.id}">
                    <div class="company-bus-list" id="company-bus-list-${company.id}">
                        ${company.buses.map(b => `<div class="bus-item">${_(b.name)}</div>`).join('')}
                    </div>
                `;
            } else {
                busList = '<small class="text-muted">No buses</small>';
            }

            const html = `
                <div class="company-card" data-id="${company.id}">
                    <div class="company-header d-flex justify-content-between align-items-center">
                        <div class="company-title">
                            <i class='bi bi-building me-2'></i> ${_(company.name)}
                        </div>
                        <div class="company-meta">${busCount} buses</div>
                    </div>
                    ${busList}
                </div>
            `;
            container.append(html);
        });

        // Pagination
        const totalPages = Math.ceil(filtered.length / pageSize);
        const pagination = $('#pagination');
        pagination.empty();
        for (let i = 1; i <= totalPages; i++) {
            const btn = $(`<span class="pagination-btn rounded-pill ${i===page?'active':''}">${i}</span>`);
            btn.on('click', () => renderPage(i, search));
            pagination.append(btn);
        }
    }

    // Per-company bus search
    $(document).on('input', '.company-bus-search', function() {
        const companyId = $(this).data('company-id');
        const query = $(this).val().toLowerCase();
        const company = allCompanies.find(c => c.id === companyId);
        if (!company) return;

        const filteredBuses = (company.buses ?? []).filter(b => b.name.toLowerCase().includes(query));
        const busList = $(`#company-bus-list-${companyId}`);
        busList.empty();
        if (filteredBuses.length > 0) {
            filteredBuses.forEach(b => busList.append(`<div class="bus-item">${_(b.name)}</div>`));
        } else {
            busList.append('<small class="text-muted">No buses found</small>');
        }
    });

    // Fetch companies
    function fetchCompanies(search = '') {
        $.get("{{ route('companies.live') }}", { search }, function(data) {
            allCompanies = data;
            renderPage(1, search);
        });
    }

    // Global search
    $('#company-search').on('input', function() {
        renderPage(1, $(this).val());
    });

    // Initial fetch + refresh every 2 minutes
    fetchCompanies();
    setInterval(() => fetchCompanies($('#company-search').val()), 120000);
</script>

<style>
    .companies-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 20px;
    }

    .company-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--card-shadow);
        transition: all 0.3s;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        cursor: pointer;
    }

    .company-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }

    .company-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .company-title {
        font-weight: 600;
        color: var(--text-color);
    }

    .company-meta {
        font-size: 0.9rem;
        color: var(--gray);
    }

    .company-bus-list {
        max-height: 160px;
        overflow-y: auto;
        margin-top: 10px;
        padding-right: 5px;
    }

    .bus-item {
        background: rgba(0, 0, 0, 0.03);
        border-radius: 6px;
        padding: 6px 10px;
        margin-bottom: 6px;
        font-size: 0.85rem;
    }

    .bus-item:hover {
        background: rgba(0, 0, 0, 0.08);
    }

    .pagination-btn {
        border: 1px solid #ddd;
        padding: 6px 12px;
        margin: 0 2px;
        cursor: pointer;
        border-radius: 4px;
        background: #fff;
    }

    .pagination-btn.active {
        background: #007bff;
        color: #fff;
    }
</style>
@endpush
