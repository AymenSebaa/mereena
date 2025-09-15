@extends('layouts.app')

@section('title', 'Zones')

@section('content')
    <div class="mobile-padding">
        <!-- Search + Add -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <input type="text" id="zoneSearch" class="form-control" placeholder="Search by name...">
            <button class="btn btn-primary w-50 m-3" data-bs-toggle="modal" data-bs-target="#upsertZoneModal" id="addZoneBtn">
                <i class="bi bi-plus-circle me-2"></i> New
            </button>
        </div>

        <!-- Zones Grid -->
        <div id="zones-container" class="tasks-container"></div>

        <!-- Pagination -->
        <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
    </div>

    <!-- Upsert Modal -->
    <div class="modal fade" id="upsertZoneModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content glass-card">
                <div class="modal-header">
                    <h5 class="modal-title">Upsert Zone</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="upsertZoneForm">
                    @csrf
                    <input type="hidden" name="id" id="zone_id">
                    <input type="hidden" name="location" id="zone_location">
                    <input type="hidden" name="geofence" id="zone_geofence">

                    <div class="modal-body row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="zone_name" required>
                            <span class="text-danger name-error"></span>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="type_id" id="zone_type_id">
                                @foreach ($types as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="zone_status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Geofence Map</label>
                            <div id="zone-map" style="height:420px; border-radius:12px;"></div>
                            <small class="text-secondary d-block mt-2">
                                Tip: first click places a marker and creates a draggable, resizable rectangle. Drag the
                                marker to move the rectangle, or drag the rectangle to move the marker.
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Zone</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteZoneModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content glass-card">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Zone</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteZoneForm" method="POST">
                    @csrf @method('delete')
                    <div class="modal-body">
                        <p>Are you sure you want to delete this zone?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // -------------------- LIST / GRID --------------------
        let zonesData = @json($zones ?? []);
        let currentPage = 1;
        const perPage = 30;

        function renderZones() {
            const container = document.getElementById('zones-container');
            container.innerHTML = '';

            const start = (currentPage - 1) * perPage;
            const pageItems = zonesData.slice(start, start + perPage);

            if (pageItems.length === 0) {
                container.innerHTML = `<div class="task-card">No zones found</div>`;
                return;
            }

            pageItems.forEach(z => {
                container.insertAdjacentHTML('beforeend', `
                <div class="task-card">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="mb-0">${_(z.name)}</h6>
                        <span class="badge ${_(z.status ? 'bg-success' : 'bg-danger')}">
                            ${_(z.status ? 'Active' : 'Inactive')}
                        </span>
                    </div>
                    <div class='d-flex justify-content-between align-items-center' >
                        <small>Type: ${_(z.type?.name ?? '-')} | Geofence: ${_(z.geofence ? 'Yes' : 'No')}</small>
                        <div class="mt-3 d-flex gap-2">
                            <a class="btn btn-sm btn-outline-primary"
                               href="{{ route('zones.index') }}/${_(z.id)}/hotels">
                                <i class="bi bi-building"></i> 
                                <small>${_(z.hotels_count ?? 0)}</small>
                            </a>
                            <button class="btn btn-sm btn-outline-warning" onclick="openEdit(${_(z.id)})">
                                <i class="bi bi-pencil-square"></i> 
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="openDelete(${_(z.id)})">
                                <i class="bi bi-trash"></i> 
                            </button>
                        </div>
                    </div>
                </div>
            `);
            });
        }

        function renderPagination() {
            const totalPages = Math.ceil(zonesData.length / perPage);
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
            renderZones();
            renderPagination();
        }

        // ---- Search ----
        document.getElementById('zoneSearch').addEventListener('input', function(e) {
            const q = e.target.value.toLowerCase();
            zonesData = @json($zones ?? []).filter(z => (z.name || '').toLowerCase().includes(q));
            currentPage = 1;
            renderZones();
            renderPagination();
        });

        // -------------------- UPSERT --------------------
        document.getElementById('upsertZoneForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = new FormData(this);

            fetch("{{ route('zones.upsert') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: form
                })
                .then(res => res.json())
                .then(data => {
                    if (data.result) location.reload();
                })
                .catch(err => console.error(err));
        });

        // -------------------- DELETE --------------------
        const deleteUrlTemplate = "{{ route('zones.delete', ['id' => '__ID__']) }}";

        function openDelete(id) {
            document.getElementById('deleteZoneForm').action = deleteUrlTemplate.replace('__ID__', id);
            new bootstrap.Modal(document.getElementById('deleteZoneModal')).show();
        }

        // -------------------- MAP & GEOFENCE --------------------
        let zoneApiReady = false;
        let zoneMap = null;
        let zoneMarker = null;
        let zonePolygon = null;
        let mapClickListener = null;
        let internalUpdate = false;

        const darkMapStyle = [ /* same as your previous darkMapStyle array */ ];

        function initZoneApi() {
            zoneApiReady = true;
        }

        function applyMapTheme() {
            if (!zoneMap) return;
            const dark = document.documentElement.getAttribute("data-theme") === "dark";
            zoneMap.setOptions({
                styles: dark ? darkMapStyle : []
            });
        }

        const defaultCenter = {
            lat: 36.7525,
            lng: 3.04197
        }; // central Algiers

        function ensureZoneMap() {
            if (!zoneMap) {
                zoneMap = new google.maps.Map(document.getElementById('zone-map'), {
                    center: defaultCenter,
                    zoom: 11,
                    gestureHandling: "cooperative",
                    mapTypeControl: false,
                    streetViewControl: false,
                });
                applyMapTheme();
            } else {
                google.maps.event.trigger(zoneMap, 'resize');
                zoneMap.setCenter(defaultCenter);
                zoneMap.setZoom(12);
                applyMapTheme();
            }
            google.maps.event.clearListeners(zoneMap, 'click');
            mapClickListener = zoneMap.addListener('click', onMapFirstClick);
        }

        function onMapFirstClick(e) {
            if (zoneMarker || zonePolygon) return;
            placePolygon(e.latLng);
        }

        function placePolygon(centerLatLng) {
            // Default small rectangle polygon
            const points = [{
                    lat: centerLatLng.lat() - 0.01,
                    lng: centerLatLng.lng() - 0.01
                },
                {
                    lat: centerLatLng.lat() - 0.01,
                    lng: centerLatLng.lng() + 0.01
                },
                {
                    lat: centerLatLng.lat() + 0.01,
                    lng: centerLatLng.lng() + 0.01
                },
                {
                    lat: centerLatLng.lat() + 0.01,
                    lng: centerLatLng.lng() - 0.01
                }
            ];

            zonePolygon = new google.maps.Polygon({
                paths: points,
                editable: true,
                draggable: true,
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.2,
                map: zoneMap
            });

            // Center marker
            const centroid = getPolygonCentroid(points);
            zoneMarker = new google.maps.Marker({
                position: centroid,
                map: zoneMap,
                draggable: true
            });

            // Marker drag moves polygon
            zoneMarker.addListener('drag', () => {
                const offsetLat = zoneMarker.getPosition().lat() - centroid.lat;
                const offsetLng = zoneMarker.getPosition().lng() - centroid.lng;
                const newPaths = zonePolygon.getPath().getArray().map(p => ({
                    lat: p.lat() + offsetLat,
                    lng: p.lng() + offsetLng
                }));
                zonePolygon.setPath(newPaths);
                updateHiddenFromPolygon();
            });

            // Polygon vertex edits
            google.maps.event.addListener(zonePolygon.getPath(), 'set_at', updateHiddenFromPolygon);
            google.maps.event.addListener(zonePolygon.getPath(), 'insert_at', updateHiddenFromPolygon);
            google.maps.event.addListener(zonePolygon, 'remove_at', updateHiddenFromPolygon);
            google.maps.event.addListener(zonePolygon, 'drag', updateHiddenFromPolygon);

            updateHiddenFromPolygon();
        }

        function updateHiddenFromPolygon() {
            if (!zonePolygon) return;
            const coords = zonePolygon.getPath().getArray().map(p => ({
                lat: p.lat(),
                lng: p.lng()
            }));
            document.getElementById('zone_geofence').value = JSON.stringify(coords);
            // Update location marker as centroid
            const centroid = getPolygonCentroid(coords);
            if (zoneMarker) zoneMarker.setPosition(centroid);
        }

        function getPolygonCentroid(coords) {
            let lat = 0,
                lng = 0;
            coords.forEach(c => {
                lat += c.lat;
                lng += c.lng;
            });
            return {
                lat: lat / coords.length,
                lng: lng / coords.length
            };
        }

        function clearShapes() {
            if (zoneMarker) {
                zoneMarker.setMap(null);
                zoneMarker = null;
            }
            if (zonePolygon) {
                zonePolygon.setMap(null);
                zonePolygon = null;
            }
        }

        // -------------------- MODAL LIFECYCLE --------------------
        const upsertModal = document.getElementById('upsertZoneModal');

        upsertModal.addEventListener('show.bs.modal', () => {
            document.querySelector('.name-error').innerText = '';
        });

        upsertModal.addEventListener('shown.bs.modal', () => {
            if (!zoneApiReady) return;
            ensureZoneMap();

            clearShapes();
            google.maps.event.clearListeners(zoneMap, 'click');
            mapClickListener = zoneMap.addListener('click', onMapFirstClick);

            const geo = (document.getElementById('zone_geofence').value || '').trim();
            const loc = (document.getElementById('zone_location').value || '').trim();

            if (geo) {
                try {
                    const points = JSON.parse(geo);
                    zonePolygon = new google.maps.Polygon({
                        paths: points,
                        editable: true,
                        draggable: true,
                        strokeColor: '#FF0000',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '#FF0000',
                        fillOpacity: 0.2,
                        map: zoneMap
                    });
                    const centroid = getPolygonCentroid(points);
                    zoneMarker = new google.maps.Marker({
                        position: centroid,
                        map: zoneMap,
                        draggable: true
                    });
                    // Bind listeners
                    zoneMarker.addListener('drag', () => {
                        const offsetLat = zoneMarker.getPosition().lat() - centroid.lat;
                        const offsetLng = zoneMarker.getPosition().lng() - centroid.lng;
                        const newPaths = zonePolygon.getPath().getArray().map(p => ({
                            lat: p.lat() + offsetLat,
                            lng: p.lng() + offsetLng
                        }));
                        zonePolygon.setPath(newPaths);
                        updateHiddenFromPolygon();
                    });
                    google.maps.event.addListener(zonePolygon.getPath(), 'set_at', updateHiddenFromPolygon);
                    google.maps.event.addListener(zonePolygon.getPath(), 'insert_at', updateHiddenFromPolygon);
                    google.maps.event.addListener(zonePolygon.getPath(), 'remove_at', updateHiddenFromPolygon);
                    google.maps.event.addListener(zonePolygon, 'drag', updateHiddenFromPolygon);
                    zoneMap.fitBounds(new google.maps.LatLngBounds(
                        points.reduce((a, p) => a.extend(p), new google.maps.LatLngBounds())
                    ));
                } catch (_) {}
            } else if (loc && loc.includes(',')) {
                const [la, ln] = loc.split(',').map(Number);
                placePolygon(new google.maps.LatLng(la, ln));
                zoneMap.setCenter({
                    lat: la,
                    lng: ln
                });
                zoneMap.setZoom(12);
            }
        });

        upsertModal.addEventListener('hidden.bs.modal', () => {
            document.getElementById('upsertZoneForm').reset();
            document.getElementById('zone_id').value = '';
            document.getElementById('zone_location').value = '';
            document.getElementById('zone_geofence').value = '';
            clearShapes();
            if (zoneMap) {
                google.maps.event.clearListeners(zoneMap, 'click');
                zoneMap.setCenter(defaultCenter);
                zoneMap.setZoom(6);
            }
        });

        const themeObserver = new MutationObserver(() => applyMapTheme());
        themeObserver.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme']
        });

        // -------------------- EDIT HANDLER --------------------
        function openEdit(id) {
            const z = (@json($zones ?? [])).find(x => Number(x.id) === Number(id));
            if (!z) return;
            document.getElementById('zone_id').value = z.id;
            document.getElementById('zone_name').value = z.name || '';
            document.getElementById('zone_status').value = z.status ? '1' : '0';
            document.getElementById('zone_type_id').value = z.type_id ?? '';
            document.getElementById('zone_location').value = z.location || '';
            document.getElementById('zone_geofence').value = z.geofence || '';
            new bootstrap.Modal(document.getElementById('upsertZoneModal')).show();
        }

        // -------------------- INIT --------------------
        renderZones();
        renderPagination();
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&callback=initZoneApi" async defer>
    </script>

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
            animation: slideUp 0.4s ease-out;
        }

        [data-theme="dark"] .task-card {
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .task-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        /* Map glass border in dark mode */
        [data-theme="dark"] #zone-map {
            border: 1px solid rgba(255, 255, 255, .08);
        }
    </style>
@endpush
