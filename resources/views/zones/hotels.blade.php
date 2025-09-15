@extends('layouts.app')

@section('title', 'Zone Hotels')

@section('content')
<div class="mobile-padding">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb glass-card p-3 mb-4">
            <li class="breadcrumb-item">
                <a href="{{ route('zones.index') }}">Zones</a>
            </li>
            <li class="breadcrumb-item active">{{ $zone->name }}</li>
        </ol>
    </nav>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="hotelTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-hotels-tab" data-bs-toggle="tab" data-bs-target="#all-hotels" type="button" role="tab">All Hotels</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="selected-hotels-tab" data-bs-toggle="tab" data-bs-target="#selected-hotels" type="button" role="tab">Selected Hotels</button>
        </li>
    </ul>

    <!-- Search -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input type="text" id="searchHotel" class="form-control" placeholder="Search hotels...">
    </div>

    <!-- Tabs Content -->
    <div class="tab-content" id="hotelTabsContent">
        <!-- All Hotels -->
        <div class="tab-pane fade show active" id="all-hotels" role="tabpanel">
            <div id="hotels-container" class="tasks-container"></div>
        </div>

        <!-- Selected Hotels -->
        <div class="tab-pane fade" id="selected-hotels" role="tabpanel">
            <div id="selected-hotels-container" class="tasks-container"></div>
        </div>
    </div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
</div>
@endsection

@push('scripts')
<style>
    .tasks-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(23em, 1fr));
        gap: 20px;
    }
    .task-card {
        background: rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--card-shadow);
        transition: all 0.3s;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.6);
    }
    [data-theme="dark"] .task-card {
        background: rgba(30, 41, 59, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .task-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }
    .disabled-card {
        opacity: 0.5;
        pointer-events: none;
    }

     /* Tabs light mode */
        .nav-tabs .nav-link {
            color: #111827;
            /* gray-900 */
        }

        /* Tabs dark mode */
        [data-theme="dark"] .nav-tabs .nav-link {
            color: #9ca3af;
            /* gray-400 for inactive */
        }

        [data-theme="dark"] .nav-tabs .nav-link.active {
            color: #f9fafb;
            /* white for active */
            background-color: transparent;
            border-color: #4b5563 #4b5563 #1e293b;
            /* subtle gray borders */
        }

        [data-theme="dark"] .breadcrumb,
        [data-theme="dark"] .breadcrumb-item.active,
        [data-theme="dark"] .breadcrumb-item::before {
            background: rgba(30, 41, 59, 0.7);
            border-radius: 8px;
            color: #f1f5f9;
        }
</style>

<script>
    const zoneId = {{ $zone->id }};
    const allHotels = @json($hotels);
    const selectedHotels = @json($zone->hotels);

    let currentPage = 1;
    const perPage = 30;
    let searchQuery = '';

    function renderHotels() {
        const container = document.getElementById('hotels-container');
        container.innerHTML = '';

        const filtered = allHotels.filter(h => h.name.toLowerCase().includes(searchQuery.toLowerCase()));

        const start = (currentPage - 1) * perPage;
        const pageItems = filtered.slice(start, start + perPage);

        if (pageItems.length === 0) {
            container.innerHTML = `<div class="task-card">No hotels found</div>`;
            return;
        }

        pageItems.forEach(hotel => {
            const attachedZone = hotel.zones.length ? hotel.zones[0].name : null;
            const isSelected = selectedHotels.some(sh => sh.id === hotel.id);
            const isDisabled = attachedZone && !isSelected;

            container.insertAdjacentHTML('beforeend', `
                <div class="task-card ${isDisabled ? 'disabled-card' : ''}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        ${attachedZone ? `<span class="badge bg-info"> 
                            <i class='bi bi-pin-map-fill' ></i> ${_(attachedZone)}
                        </span>` : '<span></span>'}
                        <button class="btn btn-sm ${isSelected ? 'btn-danger' : 'btn-success'}"
                            onclick="toggleHotel(${hotel.id}, '${isSelected ? 'detach' : 'attach'}')">
                            ${isSelected ? 'Detach' : 'Attach'}
                        </button>
                    </div>
                    <span> <i class='bi bi-building' ></i> ${_(hotel.name)}</span>
                </div>
            `);
        });
    }

    function renderSelectedHotels() {
        const container = document.getElementById('selected-hotels-container');
        container.innerHTML = '';

        if (selectedHotels.length === 0) {
            container.innerHTML = `<div class="task-card">No hotels selected</div>`;
            return;
        }

        selectedHotels.forEach(hotel => {
            container.insertAdjacentHTML('beforeend', `
                <div class="task-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6>${_(hotel.name)}</h6>
                        <button class="btn btn-sm btn-danger"
                            onclick="toggleHotel(${hotel.id}, 'detach')">
                            Detach
                        </button>
                    </div>
                </div>
            `);
        });
    }

    function renderPagination() {
        const totalPages = Math.ceil(allHotels.length / perPage);
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
            pagination.insertAdjacentHTML('beforeend', `
                <button class="btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-light'} mx-1"
                        onclick="goToPage(${i})">${i}</button>
            `);
        }
    }

    function goToPage(page) {
        currentPage = page;
        renderHotels();
        renderPagination();
    }

    function toggleHotel(hotelId, action) {
        fetch(`{{ route('zones.index') }}/${zoneId}/hotel`, {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ hotel_id: hotelId, action: action })
        })
        .then(res => res.json())
        .then(data => {
            if (data.message.includes('attached')) {
                const hotel = allHotels.find(h => h.id === hotelId);
                if (hotel) selectedHotels.push(hotel);
            } else if (data.message.includes('detached')) {
                const idx = selectedHotels.findIndex(h => h.id === hotelId);
                if (idx >= 0) selectedHotels.splice(idx, 1);
            }
            renderHotels();
            renderSelectedHotels();
        })
        .catch(err => console.error(err));
    }

    // ---- Search ----
    document.getElementById('searchHotel').addEventListener('input', function(e) {
        searchQuery = e.target.value;
        currentPage = 1;
        renderHotels();
        renderPagination();
    });

    // Init
    renderHotels();
    renderSelectedHotels();
    renderPagination();
</script>
@endpush
