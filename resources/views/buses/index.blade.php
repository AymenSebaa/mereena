@extends('layouts.app')

@section('title', 'Buses')

@section('content')
    <div class="mobile-padding">

        <!-- Map -->
        <div class="overlay-card w-100 mb-4">
            <div class="card-title">
                <span>Buses Map</span>
                <small class="text-secondary">Live location of all buses</small>
            </div>
            <div class="alerts-section p-0 overflow-hidden">
                <div id="bus-map" style="width: 100%; height: 500px; border-radius: 0 0 20px 20px;"></div>
            </div>
        </div>

        <!-- Search + QR Bulk -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <input type="text" id="bus-search" placeholder="Search buses..." class="form-control w-50">

            @if (!in_array(auth()->user()->profile->role_id, [3, 4, 10]))
                <button id="bulk-qr-btn" class="btn btn-primary">Generate All QR Codes</button>
            @endif
        </div>

        <!-- Bus Cards -->
        <div id="buses-container" class="buses-container"></div>

        <!-- Pagination -->
        <div id="pagination" class="d-flex justify-content-center mt-4"></div>

        <!-- Upsert Modal -->
        <div class="modal fade" id="upsertBusModal" tabindex="-1" aria-labelledby="upsertBusModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form id="busUpsertForm" class="modal-content">
                    @csrf
                    <input type="hidden" name="id" id="bus_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="upsertBusModalLabel">Edit Bus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="bus_name" class="form-label">Bus Name</label>
                            <input type="text" class="form-control" id="bus_name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="bus_type" class="form-label">Bus Type</label>
                            <select id="bus_type" name="type_id" class="form-select" required>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
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
        // Open modal for edit
        $(document).on('dblclick', '.bus-card', function(e) {
            e.stopPropagation();
            const busId = $(this).data('id');
            const bus = allBuses.find(b => b.id === busId);
            if (!bus) return;

            $('#bus_id').val(bus.id);
            $('#bus_name').val(bus.name);
            $('#bus_type').val(bus.type_id);

            $('#upsertBusModal').modal('show');
        });

        // Handle submit
        $('#busUpsertForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.post("{{ route('buses.upsert') }}", formData, function(res) {
                if (res.success) {
                    $('#upsertBusModal').modal('hide');
                    fetchBuses($('#bus-search').val()); // refresh list
                }
            }).fail(function(xhr) {
                alert("Error: " + xhr.responseJSON.message);
            });
        });


        let map, bounds;
        let busMarkers = []; // {id, marker, scans: []}
        let allBuses = [];
        const pageSize = 40;
        let currentPage = 1;

        // Initialize map
        function initMap() {
            map = new google.maps.Map(document.getElementById('bus-map'), {
                center: {
                    lat: 36.7601,
                    lng: 3.0503
                },
                zoom: 6,
                gestureHandling: "cooperative"
            });
            bounds = new google.maps.LatLngBounds();
        }

        // Render bus markers and scan markers
        function renderMarkers(buses) {
            busMarkers.forEach(b => b.marker.setMap(null));
            busMarkers = [];
            bounds = new google.maps.LatLngBounds();

            buses.forEach(bus => {
                if (!bus.lat || !bus.lng) return;
                const pos = {
                    lat: parseFloat(bus.lat),
                    lng: parseFloat(bus.lng)
                };
                const iconColor = bus.status === 'offline' ? 'red' : bus.status === 'ack' ? 'orange' : 'green';

                const marker = new google.maps.Marker({
                    position: pos,
                    map,
                    title: bus.name,
                    icon: `http://maps.google.com/mapfiles/ms/icons/${iconColor}-dot.png`
                });

                const infoWindow = new google.maps.InfoWindow({
                    content: `<div class='text-dark'><strong>${_(bus.name)}</strong><br>Status: ${_(bus.status)}<br>Device: ${_(bus.device_name ?? '-')}<br>Speed: ${_(bus.speed ?? '-')}, ETA: ${_(bus.eta ?? '-')}</div>`
                });

                marker.addListener('click', () => infoWindow.open(map, marker));

                // Scan markers
                const scanMarkers = [];
                if (bus.scans?.length > 0) {
                    bus.scans.forEach(s => {
                        if (!s.lat || !s.lng) return;
                        const sm = new google.maps.Marker({
                            position: {
                                lat: parseFloat(s.lat),
                                lng: parseFloat(s.lng)
                            },
                            map,
                            title: s.user?.name ?? 'Deleted Staff',
                            icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png",
                            visible: false
                        });
                        scanMarkers.push(sm);
                    });
                }

                busMarkers.push({
                    id: bus.id,
                    marker,
                    scans: scanMarkers
                });
                bounds.extend(pos);
            });

            if (!bounds.isEmpty()) map.fitBounds(bounds);
        }

        // Render bus cards with scan timeline
        function renderPage(page = 1, search = '') {
            currentPage = page;
            const filtered = allBuses.filter(bus => bus.name.toLowerCase().includes(search.toLowerCase()));
            const start = (page - 1) * pageSize;
            const end = start + pageSize;
            const pageBuses = filtered.slice(start, end);

            const container = $('#buses-container');
            container.empty();

            pageBuses.forEach(bus => {
                const scanCount = bus.scans?.length ?? 0;
                let scanList = '';

                if (scanCount > 0) {
                    // Sort scans by created_at descending
                    const sortedScans = bus.scans.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

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
                                    data-bus-id="${bus.id}" 
                                    data-lat="${s.lat}" 
                                    data-lng="${s.lng}">
                                    <div class='d-flex justify-content-between align-items-center' >
                                        <span>${_(s.user?.name ?? 'Deleted Staff')}</span>  
                                        <span> ${s.extra ? `<span class='bus-status status-generic' >${_(s.extra)}</span> `:''} ${new Date(_(s.created_at)).toLocaleTimeString()}</span>
                                    </div>
                                </button>
                            `;
                        });
                    }
                    scanList += '</div>';
                }

                const statusMap = {
                    online: ['Online', 'status-online'],
                    offline: ['Offline', 'status-offline'],
                    ack: ['Pending', 'status-pending']
                };
                const [label, statusClass] = statusMap[bus.status] ?? [bus.status, 'status-generic'];

                const html = `
                    <div class="bus-card" data-id="${bus.id}" data-lat="${bus.lat}" data-lng="${bus.lng}">
                        <div class="bus-header d-flex justify-content-between align-items-center">
                            <div class="bus-title">
                                <i class='bi bi-bus-front me-2'></i> ${_(bus.name)}
                            </div>
                            <div class="bus-status ${statusClass}">${_(label)}</div>
                        </div>

                        <div> <i class='bi bi-building' ></i> ${_(bus.company?.name ??  '-')} </div>

                        <div class="d-flex justify-content-between align-items-center mt-2 mb-2">
                            <div class="bus-meta">
                                <span><i class="bi bi-geo-alt"></i> ${_(bus.lat)}, ${_(bus.lng)}</span>
                            </div>
                            
                            <div class="d-flex align-items-center gap-2">
                                <!-- QR button -->
                                @if (!in_array(auth()->user()->profile->role_id, [3, 4, 6, 10]))
                                <a class="btn btn-sm btn-light" href="{{ env('APP_URL') }}/buses/${bus.id}/qrcode" target="_blank">
                                    <i class="bi bi-qr-code"></i> QR
                                </a>
                                @endif

                                <!-- Selection checkbox -->
                                <input type="checkbox" class="form-check-input bus-select" value="${bus.id}">
                            </div>
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
                const btn = $(`<span class="pagination-btn ${i===page?'active':''}">${i}</span>`);
                btn.on('click', () => renderPage(i, search));
                pagination.append(btn);
            }
        }

        // Fetch buses
        function fetchBuses(search = '') {
            $.get("{{ route('buses.live') }}", {
                search
            }, function(data) {
                allBuses = data;
                renderMarkers(allBuses);
                renderPage(1, search);
            });
        }

        // Search
        $('#bus-search').on('input', function() {
            renderPage(1, $(this).val());
        });

        // Bulk QR
        @if (!in_array(auth()->user()->profile->role_id, [3, 4, 10]))
            $('#bulk-qr-btn').on('click', function() {
                const selected = $('.bus-select:checked').map(function() {
                    return $(this).val();
                }).get();

                let url = "{{ route('buses.qrcodes') }}";

                if (selected.length > 0) {
                    // Generate only selected
                    url += "?ids=" + selected.join(',');
                }
                // else: no ids passed → controller generates ALL

                window.open(url, '_blank');
            });
        @endif

        // Card click → focus bus
        $(document).on('click', '.bus-card', function() {
            const card = $(this);
            const busId = card.data('id');
            const bus = busMarkers.find(b => b.id === busId);
            if (!bus) return;

            map.panTo(bus.marker.getPosition());
            map.setZoom(14);
            bus.marker.setAnimation(google.maps.Animation.BOUNCE);
            setTimeout(() => bus.marker.setAnimation(null), 1400);
            card.addClass('highlight-card');
            setTimeout(() => card.removeClass('highlight-card'), 600);
        });

        // Scan item click → show scan marker
        $(document).on('click', '.scan-item', function(e) {
            e.stopPropagation();
            const busId = parseInt($(this).data('bus-id'));
            const lat = parseFloat($(this).data('lat'));
            const lng = parseFloat($(this).data('lng'));

            const bus = busMarkers.find(b => b.id === busId);
            if (!bus) return;

            const scanMarker = bus.scans.find(sm => sm.getPosition().lat() === lat && sm.getPosition().lng() ===
                lng);
            if (!scanMarker) return;

            scanMarker.setVisible(true);
            map.panTo(scanMarker.getPosition());
            map.setZoom(16);
            scanMarker.setAnimation(google.maps.Animation.BOUNCE);
            setTimeout(() => scanMarker.setAnimation(null), 1400);
        });

        // Initial fetch + refresh every minute
        fetchBuses();
        setInterval(() => fetchBuses($('#bus-search').val()), 60000);
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&callback=initMap" async defer>
    </script>

    <style>
        .buses-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .bus-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            cursor: pointer;
        }

        .bus-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .bus-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .bus-title {
            font-weight: 600;
            color: var(--text-color);
        }

        .bus-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-online {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
        }

        .status-offline {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger);
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning);
        }

        .status-generic {
            background: rgba(107, 114, 128, 0.15);
            color: var(--gray);
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

        .scan-timeline-container {
            max-height: 180px;
            /* scrollable */
            overflow-y: auto;
            padding-right: 5px;
        }

        .scan-day-header {
            font-size: 0.85rem;
            font-weight: bold;
            margin-top: 5px;
        }

        .scan-item {
            width: 100%;
            text-align: left;
            font-size: 0.8rem;
        }
    </style>
@endpush
