@extends('layouts.app')

@section('title', 'Sites')

@section('content')
<div class="mobile-padding">

    <!-- Search + New -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input type="text" id="site-search" placeholder="Search sites..." class="form-control rounded-pill w-50">
        <button class="btn btn-primary rounded-pill ms-3" onclick="openNewSite()">
            <i class="bi bi-plus-lg"></i> New Site
        </button>
    </div>

    <!-- Site Cards -->
    <div id="dl-container" class="dls-container"></div>

    <!-- Pagination -->
    <div id="pagination" class="d-flex justify-content-center mt-4"></div>

    @include('sites.upsert-modal')
    @include('sites.delete-modal')
</div>
@endsection

@push('scripts')
<script>
let allSites = [];
const pageSize = 30;
let currentPage = 1;

// Render page
function renderPage(page=1, search='') {
    currentPage = page;
    const filtered = allSites.filter(h =>
        (h.name ?? '').toLowerCase().includes(search.toLowerCase()) ||
        (h.address ?? '').toLowerCase().includes(search.toLowerCase())
    );

    const start = (page-1)*pageSize;
    const pageSites = filtered.slice(start,start+pageSize);

    const container = $('#dl-container').empty();
    pageSites.forEach(site => {
        const html = `
        <div class="dl-card" data-id="${site.id}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="dl-title">${_(site.name)}</div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-warning rounded-pill edit-site-btn" data-site='${JSON.stringify(site)}'>
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="openDeleteSite(${site.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="dl-details mb-2">
                <small class='text-muted' ><i class="bi bi-house me-1"></i> ${_(site.address)}</small>
                <p><i class="bi bi-geo-alt me-1"></i> ${_(site.lat)}, ${_(site.lng)}</p>
            </div>
        </div>`;
        container.append(html);
    });

    // Pagination
    const totalPages = Math.ceil(filtered.length / pageSize);
    const pagination = $('#pagination').empty();
    for(let i=1;i<=totalPages;i++){
        const btn = $(`<span class="pagination-btn rounded-pill ${i===page?'active':''}">${i}</span>`);
        btn.on('click',()=>renderPage(i,search));
        pagination.append(btn);
    }

    // Attach edit click
    $('.edit-site-btn').off('click').on('click', function(){
        const site = $(this).data('site');
        openEditSite(site);
    });
}

// Fetch sites
function fetchSites(search='') {
    $.get("{{ route('sites.index') }}", {search}, function(data){
        allSites = data;
        renderPage(1, search);
    });
}

// Search
$('#site-search').on('input', function(){
    renderPage(1, $(this).val());
});

// Initial fetch
fetchSites();
</script>
@endpush
