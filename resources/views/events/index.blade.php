@extends('layouts.app')

@section('title', 'Events')

@section('content')
<div class="mobile-padding">
    <!-- Map -->
    <div class="overlay-card w-100 mb-4">
        <div class="card-title">
            <span>Events Map</span>
            <small class="text-secondary">Live location of all events</small>
        </div>
        <div class="alerts-section p-0 overflow-hidden">
            <div id="events-map" style="width: 100%; height: 500px; border-radius: 0 0 20px 20px;"></div>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control"
               placeholder="Search by bus, hotel, message...">
    </div>

    <!-- Event Cards -->
    <div id="events-container" class="events-container"></div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
</div>
@endsection

@push('scripts')
<style>
    .events-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        margin-top: 20px;
    }

    @media (min-width: 768px) {
        .events-container { grid-template-columns: repeat(2, 1fr); }
    }

    @media (min-width: 1200px) {
        .events-container { grid-template-columns: repeat(3, 1fr); }
    }

    .event-card {
        background: rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--card-shadow);
        transition: all 0.3s;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        animation: slideUp 0.5s ease-out;
    }
    [data-theme="dark"] .event-card {
        background: rgba(30, 41, 59, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }
    .event-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }
    .event-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-complete { background: rgba(16,185,129,0.15); color: var(--success); }
    .status-new { background: rgba(99,102,241,0.15); color: var(--primary); }
    .status-zone { background: rgba(245,158,11,0.15); color: var(--warning); }
    .status-overspeed { background: rgba(239,68,68,0.15); color: var(--danger); }
    .status-generic { background: rgba(107,114,128,0.15); color: var(--gray); }
</style>

<script>
    let eventsData = [];
    let currentPage = 1;
    const perPage = 9;

    async function fetchEvents(search = '') {
        const res = await fetch(`{{ oRoute('events.live') }}?search=${_(search)}`);
        eventsData = await res.json();
        renderEvents();
        renderPagination();
        renderMap();
    }

    function renderEvents() {
        const container = document.getElementById('events-container');
        container.innerHTML = '';

        const start = (currentPage - 1) * perPage;
        const pageItems = eventsData.slice(start, start + perPage);

        if (pageItems.length === 0) {
            container.innerHTML = `<div class="event-card"><div class="event-header">
                <div class="event-title">No events</div>
                <div class="event-status status-generic">—</div>
            </div><p>You’re all caught up 🎉</p></div>`;
            return;
        }

        pageItems.forEach(event => {
            let type = event.type ? event.type.toLowerCase() : '';
            const statusMap = {
                'task_complete': {label: 'Completed', class: 'status-complete'},
                'task_new': {label: 'New Task', class: 'status-new'},
                'zone_in': {label: 'Zone In', class: 'status-zone'},
                'zone_out': {label: 'Zone Out', class: 'status-zone'},
                'overspeed': {label: 'Overspeed', class: 'status-overspeed'},
            };
            const status = statusMap[type] ?? {label: type, class: 'status-generic'};

            container.innerHTML += `
                <div class="event-card">
                    <div class="event-header">
                        <div class="event-title">${_(event.bus?.name ?? 'Unknown Bus')}</div>
                        <div class="event-status ${status.class}">${_(status.label)}</div>
                    </div>
                    <div class="event-details">
                        <p><i class="bi bi-building me-1"></i> ${_(event.hotel?.name ?? '-')}</p>
                        <p><strong>Message:</strong> ${_(event.message ?? '-')}</p>
                        <p><strong>Speed:</strong> ${_(event.speed ?? '-')}</p>
                        <p><strong>Time:</strong> ${_(event.time ?? '-')}</p>
                    </div>
                    <div class="event-meta">
                        <span><i class="bi bi-geo-alt"></i> ${_(event.latitude ?? '-')}, ${_(event.longitude ?? '-')}</span>
                    </div>
                </div>
            `;
        });
    }

    function renderPagination() {
        const totalPages = Math.ceil(eventsData.length / perPage);
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            pagination.innerHTML += `
                <button class="btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-light'} mx-1"
                        onclick="goToPage(${i})">${i}</button>
            `;
        }
    }

    function goToPage(page) {
        currentPage = page;
        renderEvents();
        renderPagination();
    }

    function renderMap() {
        const map = new google.maps.Map(document.getElementById('events-map'), {
            center: { lat: 36.7601, lng: 3.0503 },
            zoom: 6,
            gestureHandling: "cooperative"
        });
        const bounds = new google.maps.LatLngBounds();

        eventsData.forEach(event => {
            if (event.latitude && event.longitude) {
                const pos = { lat: Number(event.latitude), lng: Number(event.longitude) };
                let iconColor = 'grey';
                if (event.type === 'task_complete') iconColor = 'green';
                else if (event.type === 'task_new') iconColor = 'blue';
                else if (['zone_in', 'zone_out'].includes(event.type)) iconColor = 'yellow';
                else if (event.type === 'overspeed') iconColor = 'red';

                const marker = new google.maps.Marker({
                    position: pos,
                    map,
                    title: `${_(event.type)} - ${_(event.message)}`,
                    icon: `http://maps.google.com/mapfiles/ms/icons/${iconColor}-dot.png`
                });

                const infowindow = new google.maps.InfoWindow({
                    content: `<div class="p-2">
                        <strong>${_(event.type)}</strong><br>
                        ${_(event.message)}<br>
                        <small>${_(event.time)}</small><br>
                        <em>Bus: ${_(event.bus?.name ?? '-')}</em><br>
                        <em>Hotel: ${_(event.hotel?.name ?? '-')}</em>
                    </div>`
                });

                marker.addListener("click", () => infowindow.open(map, marker));
                bounds.extend(pos);
            }
        });

        if (!bounds.isEmpty()) map.fitBounds(bounds);
    }

    document.getElementById('searchInput').addEventListener('input', (e) => {
        fetchEvents(e.target.value);
    });

    fetchEvents(); // initial load
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&callback=renderMap" async defer></script>
@endpush
