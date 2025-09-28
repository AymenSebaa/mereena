@extends('layouts.app')

@section('title', 'Hotels')

@section('content')
    <div class="mobile-padding">
        <!-- Map -->
        <div class="overlay-card w-100 mb-4">
            <div class="card-title">
                <span>Hotels Map</span>
                <small class="text-secondary">Live location of all hotels</small>
            </div>
            <div class="alerts-section p-0 overflow-hidden">
                <div id="hotel-map" style="width: 100%; height: 500px; border-radius: 0 0 20px 20px;"></div>
            </div>
        </div>

        <!-- Search + Bulk QR -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <input type="text" id="hotel-search" placeholder="Search hotels..." class="form-control w-50">
            @if (!in_array(auth()->user()->profile->role_id, [3, 4, 10]))
                <button id="bulk-qr-btn" class="btn btn-primary">Generate All QR Codes</button>
            @endif
        </div>

        <!-- Hotel Cards -->
        <div id="hotels-container" class="hotels-container"></div>

        <!-- Pagination -->
        <div id="pagination" class="d-flex justify-content-center mt-4"></div>

    </div>
@endsection

@push('scripts')
    <script>
        let map, bounds, markers = {},
            allHotels = [];
        const pageSize = 30;
        let currentPage = 1;

        // Init map
        function initMap() {
            map = new google.maps.Map(document.getElementById('hotel-map'), {
                center: {
                    lat: 36.7601,
                    lng: 3.0503
                },
                zoom: 6,
                gestureHandling: "cooperative"
            });
            bounds = new google.maps.LatLngBounds();
        }

        // Render markers
        function renderMarkers(hotels) {
            markers = {};
            bounds = new google.maps.LatLngBounds();

            hotels.forEach(hotel => {
                if (hotel.lat && hotel.lng) {
                    const pos = {
                        lat: Number(hotel.lat),
                        lng: Number(hotel.lng)
                    };
                    const marker = new google.maps.Marker({
                        position: pos,
                        map,
                        title: _(hotel.name),
                        icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                    });
                    const infoWindow = new google.maps.InfoWindow({
                        content: `<div class="text-dark"><strong>${_(hotel.name)}</strong><br>${_(hotel.address ?? '')}</div>`
                    });
                    marker.addListener('click', () => infoWindow.open(map, marker));
                    markers[hotel.id] = marker;
                    bounds.extend(pos);
                }
            });

            if (!bounds.isEmpty()) map.fitBounds(bounds);
        }

        // Render hotel cards with scan timeline
        function renderPage(page = 1, search = '') {
            currentPage = page;
            const filtered = allHotels.filter(h =>
                (h.name ?? '').toLowerCase().includes(search.toLowerCase()) ||
                (h.address ?? '').toLowerCase().includes(search.toLowerCase())
            );

            const start = (page - 1) * pageSize;
            const end = start + pageSize;
            const pageHotels = filtered.slice(start, end);

            const container = $('#hotels-container');
            container.empty();

            pageHotels.forEach(hotel => {
                const scanCount = hotel.scans?.length ?? 0;
                let scanList = '';

                if (scanCount > 0) {
                    // Sort scans by created_at descending
                    const sortedScans = hotel.scans.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

                    // Group scans by day
                    const scansByDay = {};
                    sortedScans.forEach(s => {
                        const day = new Date(s.created_at).toLocaleDateString();
                        if (!scansByDay[day]) scansByDay[day] = [];
                        scansByDay[day].push(s);
                    });

                    scanList = '<div class="scan-timeline-container">';
                    for (const day in scansByDay) {
                        scanList += `<div class="scan-day-header text-center p-2 w-100"> - ${day} - </div>`;
                        scansByDay[day].forEach(s => {
                            scanList += `
                        <button class="btn btn-sm btn-light scan-item mb-1" 
                            data-hotel-id="${hotel.id}" 
                            data-lat="${s.lat}" 
                            data-lng="${s.lng}">
                            <div class='d-flex justify-content-between align-items-center'>
                                <span>${_(s.user?.name ?? 'Deleted Staff')}</span>  
                                <span>${new Date(_(s.created_at)).toLocaleTimeString()}</span>
                            </div>
                        </button>
                    `;
                        });
                    }
                    scanList += '</div>';
                }

                const html = `
                    <div class="hotel-card" data-id="${hotel.id}" data-lat="${hotel.lat}" data-lng="${hotel.lng}">
                        <div class="hotel-header d-flex justify-content-between align-items-center mb-2">
                            <div class="hotel-title">${_(hotel.name)}</div>
                        </div>
                        <div class="hotel-details mb-2">
                            <p><i class="bi bi-geo-alt me-1"></i> ${_(hotel.lat)}, ${_(hotel.lng)}</p>
                        </div>
                        
                        <div class="hotel-meta mt-2 mb-2">
                            <a href="https://www.google.com/maps/search/?api=1&query=${_(hotel.lat)},${_(hotel.lng)}" target="_blank" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-map"></i> Open in Maps
                            </a>
                            @if (!in_array(auth()->user()->profile->role_id, [3, 4, 10]))
                                <a href="{{ env('APP_URL') }}/hotels/${hotel.id}/qrcode" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-qr-code"></i> QR
                                </a>
                            @endif
                        </div>

                        <h6>Scans (${scanCount}):</h6>
                        ${scanList}
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

        // Scan item click → show scan marker
        $(document).on('click', '.scan-item', function(e) {
            e.stopPropagation();
            const hotelId = parseInt($(this).data('hotel-id'));
            const lat = parseFloat($(this).data('lat'));
            const lng = parseFloat($(this).data('lng'));

            if (!markers[hotelId]) return;

            const scanMarker = new google.maps.Marker({
                position: {
                    lat,
                    lng
                },
                map,
                title: 'Scan',
                icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png",
                animation: google.maps.Animation.BOUNCE
            });

            map.panTo({
                lat,
                lng
            });
            map.setZoom(16);

            setTimeout(() => scanMarker.setAnimation(null), 1400);
        });


        // Fetch hotels from backend
        function fetchHotels(search = '') {
            $.get("{{ route('hotels.live') }}", {
                search
            }, function(data) {
                allHotels = data;
                renderMarkers(allHotels);
                renderPage(1, search);
            });
        }

        // Search
        $('#hotel-search').on('input', function() {
            renderPage(1, $(this).val());
        });

        @if (!in_array(auth()->user()->profile->role_id, [3, 4, 10]))
            // Bulk QR
            $('#bulk-qr-btn').on('click', function() {
                window.open("{{ route('hotels.qrcodes') }}", '_blank');
            });
        @endif

        // Card click
        $(document).on('click', '.hotel-card', function() {
            const card = $(this);
            const lat = parseFloat(card.data('lat'));
            const lng = parseFloat(card.data('lng'));
            const id = card.data('id');
            if (!isNaN(lat) && !isNaN(lng)) {
                document.getElementById('hotel-map').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                map.setCenter({
                    lat,
                    lng
                });
                map.setZoom(12);
                if (markers[id]) google.maps.event.trigger(markers[id], 'click');
                card.addClass('highlight-card');
                setTimeout(() => card.removeClass('highlight-card'), 600);
            }
        });

        // Initial fetch + auto refresh
        fetchHotels();
        setInterval(() => fetchHotels($('#hotel-search').val()), 60000);
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&callback=initMap" async defer>
    </script>

    <style>
        .hotels-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        @media (min-width: 768px) {
            .hotels-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1200px) {
            .hotels-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .hotel-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            cursor: pointer;
        }

        .hotel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .highlight-card {
            animation: highlight 0.6s ease;
        }

        @keyframes highlight {
            0% {
                background-color: rgba(255, 255, 0, 0.3);
            }

            100% {
                background-color: transparent;
            }
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
