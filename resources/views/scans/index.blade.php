@extends('layouts.app')

@section('title', 'Scans')
@php
    $role_id = auth()->user()->profile->role_id;
@endphp

@section('content')
    <div class="mobile-padding" data-role="{{ $role_id }}">

        <!-- Map -->
        <div class="overlay-card w-100 mb-4">
            <div class="card-title d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <span>Live scans</span>
                </div>

                <div class="d-flex align-items-center gap-2 ms-3">
                    <input type="date" id="from-date" class="form-control form-control-sm"
                        value="{{ request('from', now()->toDateString()) }}">
                    <span>to</span>
                    <input type="date" id="to-date" class="form-control form-control-sm"
                        value="{{ request('to', now()->toDateString()) }}">
                    <button id="filter-date" class="btn btn-outline-secondary btn-sm">Apply</button>
                </div>

                <button id="toggle-view" class="btn btn-primary btn-sm">Switch to Table</button>
            </div>
            <div class="alerts-section p-0 overflow-hidden">
                <div id="scan-map" style="width: 100%; height: 500px; border-radius: 0 0 20px 20px;"></div>
            </div>
        </div>

        <!-- Cards/Search/Pagination -->
        @include('scans.cards')

        <!-- Table -->
        @include('scans.table')

    </div>
@endsection
@push('scripts')
    <script>
        // Haversine formula (same as cards)
        function calcDistance(lat_1, lon_1, lat_2, lon_2) {
            let lat1 = parseFloat(lat_1);
            let lon1 = parseFloat(lon_1);
            let lat2 = parseFloat(lat_2);
            let lon2 = parseFloat(lon_2);

            if (!lat1 || !lon1 || !lat2 || !lon2) return null;
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) *
                Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return (R * c).toFixed(2);
        }

        // Toggle button
        $('#toggle-view').on('click', function() {
            isTableView = !isTableView;
            if (isTableView) {
                $('#cards-wrapper').hide(); // hide search+cards+pagination
                $('#scans-table-container').show();
                $(this).text('Switch to Cards');


                if (!dataTable) {
                    dataTable = $('#scans-table').DataTable({
                        pageLength: 30,
                        responsive: true,
                        order: [
                            [7, 'desc']
                        ],
                    });

                    // 🔑 On table draw (search, filter, paginate)
                    dataTable.on('draw', function() {
                        const visibleScanIds = [];
                        dataTable.rows({
                            filter: 'applied'
                        }).every(function() {
                            const node = this.node();
                            const id = $(node).attr('data-scan-id');
                            if (id) visibleScanIds.push(parseInt(id));
                        });

                        const filteredScans = allScans.filter(s => visibleScanIds.includes(s.id));
                        renderMarkers(filteredScans);
                    });
                }

                renderTable(allScans);
            } else {
                $('#scans-table-container').hide();
                $('#cards-wrapper').show(); // show search+cards+pagination
                $(this).text('Switch to Table');
                renderPage(1, $('#scan-search').val());
            }
        });

        $(document).ready(function() {
            const roleId = parseInt($('.mobile-padding').data('role'));
            if (roleId === 1 || roleId === 2) {
                $('#toggle-view').trigger('click'); // table by default
            } else {
                $('#scans-table-container').hide();
                $('#cards-wrapper').show();
                $('#toggle-view').text('Switch to Table');
            }
        });

        let map, bounds, scanMarkers = [],
            allScans = [];
        const pageSize = 30;
        let currentPage = 1;

        // --- Marker colors based on extra ---
        const colorMap = {
            arrival: "#3b82f6", // blue
            departure: "#10b981", // green
            boarding: "#f59e0b", // yellow
            none: "#6b7280" // gray
        };

        function getCustomMarker(color) {
            return {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 8,
                fillColor: color,
                fillOpacity: 0.9,
                strokeWeight: 2,
                strokeColor: "#fff" // makes the circle pop
            };
        }

        // Initialize map
        function initMap() {
            map = new google.maps.Map(document.getElementById('scan-map'), {
                center: {
                    lat: 36.7601,
                    lng: 3.0503
                },
                zoom: 12,
                gestureHandling: "cooperative"
            });
            // bounds = new google.maps.LatLngBounds();
        }

        // Render scan markers
        function renderMarkers(scans) {
            scanMarkers.forEach(s => s.setMap(null));
            scanMarkers = [];
            // bounds = new google.maps.LatLngBounds();

            scans.forEach(scan => {
                if (!scan.lat || !scan.lng) return;
                const pos = {
                    lat: parseFloat(scan.lat),
                    lng: parseFloat(scan.lng)
                };

                // Pick color based on scan.extra
                const extra = scan.extra || 'none';
                const markerColor = colorMap[extra] || colorMap['none'];

                const marker = new google.maps.Marker({
                    position: pos,
                    map,
                    title: _(scan.user?.name ?? 'Deleted User'),
                    icon: getCustomMarker(markerColor)
                });

                const infoWindow = new google.maps.InfoWindow({
                    content: `<div class='text-dark'>
                    <strong>${_(scan.user?.name ?? '-')}</strong> (${_(scan.user?.profile?.role?.name ?? '-')})<br>
                    Hotel: ${_(scan.user?.profile?.hotel?.name ?? '-') }<br>
                    Distance: ${
                        scan.user?.profile?.hotel?.lat && scan.user?.profile?.hotel?.lng
                            ? calcDistance(scan.lat, scan.lng,
                                           scan.user.profile.hotel.lat,
                                           scan.user.profile.hotel.lng) + ' km'
                            : '-'
                    }<br>
                    Type: ${_(scan.type)}<br>
                    Name: ${_(scan.bus?.name ?? scan.hotel?.name ?? scan.guest?.name ?? '')}<br>
                    At: ${new Date(_(scan.created_at)).toLocaleString()}
                </div>`
                });

                marker.addListener('click', () => infoWindow.open(map, marker));
                scanMarkers.push(marker);
                // bounds.extend(pos);
            });

            // if (!bounds.isEmpty()) map.fitBounds(bounds);
        }

        // Fetch scans with search + date filter
        function fetchScans(search = '') {
            $.get("{{ route('scans.live') }}", {
                search,
                from: $('#from-date').val(),
                to: $('#to-date').val()
            }, function(data) {
                allScans = data;

                if (isTableView) {
                    renderTable(allScans);

                    // keep markers in sync with current search filter
                    const visibleScanIds = [];
                    dataTable.rows({
                        filter: 'applied'
                    }).every(function() {
                        const node = this.node();
                        const id = $(node).attr('data-scan-id');
                        if (id) visibleScanIds.push(parseInt(id));
                    });
                    const filteredScans = allScans.filter(s => visibleScanIds.includes(s.id));
                    renderMarkers(filteredScans);
                } else {
                    renderPage(1, search);
                    renderMarkers(allScans); // cards mode = show all markers
                }
            });
        }

        // Date filter button
        $('#filter-date').on('click', function() {
            fetchScans($('#scan-search').val());
        });

        // Initial fetch + auto refresh
        const refreshDelay = 1000 * 60;
        fetchScans();
        setInterval(() => fetchScans($('#scan-search').val()), refreshDelay);
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&callback=initMap" async defer>
    </script>

    <style>
        .scans-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        @media (min-width: 768px) {
            .scans-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1200px) {
            .scans-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .scan-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            cursor: pointer;
        }

        .scan-card:hover {
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

        .scan-timeline-container {
            max-height: 180px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .scan-item {
            text-align: left;
            font-size: 0.8rem;
        }

        /* Background + text for extra class (table rows, cards, etc.) */
        .extra-arrival {
            background: rgba(59, 130, 246, 0.15);
            /* blue-500 15% */
            color: #3b82f6;
            /* blue-500 */
        }

        .extra-departure {
            background: rgba(16, 185, 129, 0.15);
            /* green-500 15% */
            color: #10b981;
            /* green-500 */
        }

        .extra-boarding {
            background: rgba(245, 158, 11, 0.15);
            /* yellow-500 15% */
            color: #f59e0b;
            /* yellow-500 */
        }

        .extra-none {
            background: rgba(107, 114, 128, 0.15);
            /* gray-500 15% */
            color: #6b7280;
            /* gray-500 */
        }

        /* Badge versions (for small inline badges) */
        .badge-extra {
            display: inline-block;
            padding: 0.25em 0.5em;
            border-radius: 1em;
            font-size: .8em;
            text-transform: capitalize;
        }

        .badge-extra.arrival {
            background-color: #3b82f6;
            /* blue */
            color: white;
        }

        .badge-extra.departure {
            background-color: #10b981;
            /* green */
            color: white;
        }

        .badge-extra.boarding {
            background-color: #f59e0b;
            /* yellow */
            color: white;
        }

        .badge-extra.none {
            background-color: #6b7280;
            /* gray */
            color: white;
        }
    </style>
@endpush
