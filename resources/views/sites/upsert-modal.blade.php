<button class="btn btn-primary rounded-pill ms-3" onclick="openNewSite()">
    <i class="bi bi-plus-lg"></i> New Site
</button>

<!-- Upsert Modal -->
<div class="modal fade" id="upsertSiteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title" id="siteModalTitle">Upsert Site</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="upsertSiteForm">
                @csrf
                <input type="hidden" name="id" id="site_id">
                <input type="hidden" name="lat" id="site_lat">
                <input type="hidden" name="lng" id="site_lng">
                <input type="hidden" name="geofence" id="site_geofence">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-control rounded-pill" name="type_id" id="site_type_id">
                            @foreach ($types as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control rounded-pill" name="name" id="site_name" required>
                    </div>
                    <div class="mb-3">
                        <label>Address</label>
                        <input type="text" class="form-control rounded-pill" name="address" id="site_address">
                    </div>
                    <div class="mb-3">
                        <label>Site Location & Geofence</label>
                        <div id="site-map" style="height: 400px; border-radius: 10px;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success rounded-pill" type="submit" >Save Site</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let siteMap, siteMarker, sitePolygon, mapClickListener, siteModal;

    // Open new site modal
    function openNewSite() {
        $('#upsertSiteForm')[0].reset();
        $('#site_id').val('');
        siteModal = new bootstrap.Modal(document.getElementById('upsertSiteModal'));
        siteModal.show();
    }

    // Open edit modal
    function openEditSite(site) {
        $('#upsertSiteForm')[0].reset();
        $('#site_id').val(site.id);
        $('#site_type_id').val(site.type_id);
        $('#site_name').val(site.name);
        $('#site_address').val(site.address);
        $('#site_lat').val(site.lat);
        $('#site_lng').val(site.lng);
        $('#site_geofence').val(site.geofence ?? '');
        siteModal = new bootstrap.Modal(document.getElementById('upsertSiteModal'));
        siteModal.show();
    }

    // Init map when modal is shown
    $('#upsertSiteModal').on('shown.bs.modal', function() {
        if (!siteMap) {
            siteMap = new google.maps.Map(document.getElementById('site-map'), {
                center: { lat: 36.7525, lng: 3.04197 },
                zoom: 11,
                gestureHandling: "cooperative",
                mapTypeControl: false,
                streetViewControl: false
            });
        }
        clearSiteShapes();

        const geo = $('#site_geofence').val().trim();
        const lat = parseFloat($('#site_lat').val());
        const lng = parseFloat($('#site_lng').val());

        if (geo) {
            try {
                const points = JSON.parse(geo);
                placeSitePolygon(points);
                siteMap.fitBounds(points.reduce((a, p) => a.extend(p), new google.maps.LatLngBounds()));
            } catch (_) {}
        } else if (!isNaN(lat) && !isNaN(lng)) {
            placeSitePolygon([
                { lat: lat-0.01, lng: lng-0.01 },
                { lat: lat-0.01, lng: lng+0.01 },
                { lat: lat+0.01, lng: lng+0.01 },
                { lat: lat+0.01, lng: lng-0.01 }
            ]);
            siteMap.setCenter({ lat, lng });
            siteMap.setZoom(12);
        }

        google.maps.event.clearListeners(siteMap, 'click');
        mapClickListener = siteMap.addListener('click', onSiteMapClick);
    });

    // Map click to place new polygon if empty
    function onSiteMapClick(e) {
        if (sitePolygon) return;
        placeSitePolygon([
            { lat: e.latLng.lat()-0.01, lng: e.latLng.lng()-0.01 },
            { lat: e.latLng.lat()-0.01, lng: e.latLng.lng()+0.01 },
            { lat: e.latLng.lat()+0.01, lng: e.latLng.lng()+0.01 },
            { lat: e.latLng.lat()+0.01, lng: e.latLng.lng()-0.01 }
        ]);
    }

    // Place polygon + centroid marker
    function placeSitePolygon(points) {
        sitePolygon = new google.maps.Polygon({
            paths: points,
            editable: true,
            draggable: true,
            strokeColor: '#007bff',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#007bff',
            fillOpacity: 0.2,
            map: siteMap
        });

        const centroid = getCentroid(points);
        siteMarker = new google.maps.Marker({
            position: centroid,
            map: siteMap,
            draggable: true
        });

        // Drag marker updates polygon
        siteMarker.addListener('drag', () => {
            if (!sitePolygon) return;
            const centroidBefore = getCentroid(sitePolygon.getPath().getArray().map(p => ({ lat: p.lat(), lng: p.lng() })));
            const offsetLat = siteMarker.getPosition().lat() - centroidBefore.lat;
            const offsetLng = siteMarker.getPosition().lng() - centroidBefore.lng;

            const newPaths = sitePolygon.getPath().getArray().map(p => ({
                lat: p.lat() + offsetLat,
                lng: p.lng() + offsetLng
            }));

            sitePolygon.setPath(newPaths);
            updateSiteHiddenFields();
        });

        // Drag polygon updates marker and hidden fields
        sitePolygon.addListener('drag', () => {
            updateSiteHiddenFields();
            if (siteMarker) {
                const centroid = getCentroid(sitePolygon.getPath().getArray().map(p => ({ lat: p.lat(), lng: p.lng() })));
                siteMarker.setPosition(centroid);
            }
        });

        // Polygon vertex edits
        ['set_at', 'insert_at', 'remove_at'].forEach(ev =>
            google.maps.event.addListener(sitePolygon.getPath(), ev, () => {
                updateSiteHiddenFields();
                if (siteMarker) {
                    const centroid = getCentroid(sitePolygon.getPath().getArray().map(p => ({ lat: p.lat(), lng: p.lng() })));
                    siteMarker.setPosition(centroid);
                }
            })
        );

        updateSiteHiddenFields();
    }

    // Update hidden inputs
    function updateSiteHiddenFields() {
        if (!sitePolygon) return;
        const coords = sitePolygon.getPath().getArray().map(p => ({ lat: p.lat(), lng: p.lng() }));
        $('#site_geofence').val(JSON.stringify(coords));
        const centroid = getCentroid(coords);
        $('#site_lat').val(centroid.lat);
        $('#site_lng').val(centroid.lng);
    }

    // Clear shapes
    function clearSiteShapes() {
        if (sitePolygon) { sitePolygon.setMap(null); sitePolygon = null; }
        if (siteMarker) { siteMarker.setMap(null); siteMarker = null; }
    }

    // Simple centroid
    function getCentroid(coords) {
        let lat=0,lng=0;
        coords.forEach(c => { lat+=c.lat; lng+=c.lng; });
        return { lat: lat/coords.length, lng: lng/coords.length };
    }

    // Save form
    $('#upsertSiteForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.post("{{ route('sites.upsert') }}", formData, function(res) {
            if (res.result) {
                siteModal.hide();
                fetchSites($('#site-search').val());
                toastr.success(res.message);
            } else toastr.error('Failed to save site');
        }).fail(err => toastr.error(err.responseJSON?.message ?? 'Validation error'));
    });
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}" async defer></script>
