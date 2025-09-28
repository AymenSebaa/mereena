<div class="mb-3">
    <label>Site Location & Geofence</label>
    <div id="{{ $mapId ?? 'site-map' }}" style="height: 400px; border-radius: 10px;"></div>
</div>

@push('scripts')
<script>
let {{ $mapVar ?? 'siteMap' }}, {{ $markerVar ?? 'siteMarker' }}, {{ $polygonVar ?? 'sitePolygon' }};

function initGeofence() {
    const mapId = '#{{ $mapId ?? 'site-map' }}';
    if (!window['{{ $mapVar ?? 'siteMap' }}']) {
        window['{{ $mapVar ?? 'siteMap' }}'] = new google.maps.Map(document.getElementById(mapId.substring(1)), {
            center: { lat: 36.7525, lng: 3.04197 },
            zoom: 11,
            gestureHandling: "cooperative",
            mapTypeControl: false,
            streetViewControl: false
        });
    }
    clearGeofenceShapes();

    const geo = $('#site_geofence').val()?.trim();
    const lat = parseFloat($('#site_lat').val());
    const lng = parseFloat($('#site_lng').val());

    if (geo) {
        try {
            const points = JSON.parse(geo);
            placeGeofencePolygon(points);
            window['{{ $mapVar ?? 'siteMap' }}'].fitBounds(points.reduce((a, p) => a.extend(p), new google.maps.LatLngBounds()));
        } catch (_) {}
    } else if (!isNaN(lat) && !isNaN(lng)) {
        placeGeofencePolygon([
            {lat: lat-0.01, lng: lng-0.01},
            {lat: lat-0.01, lng: lng+0.01},
            {lat: lat+0.01, lng: lng+0.01},
            {lat: lat+0.01, lng: lng-0.01}
        ]);
        window['{{ $mapVar ?? 'siteMap' }}'].setCenter({lat,lng});
        window['{{ $mapVar ?? 'siteMap' }}'].setZoom(12);
    }

    google.maps.event.clearListeners(window['{{ $mapVar ?? 'siteMap' }}'], 'click');
    window['{{ $mapVar ?? 'siteMap' }}'].addListener('click', function(e){
        if(window['{{ $polygonVar ?? 'sitePolygon' }}']) return;
        placeGeofencePolygon([
            {lat: e.latLng.lat()-0.01, lng: e.latLng.lng()-0.01},
            {lat: e.latLng.lat()-0.01, lng: e.latLng.lng()+0.01},
            {lat: e.latLng.lat()+0.01, lng: e.latLng.lng()+0.01},
            {lat: e.latLng.lat()+0.01, lng: e.latLng.lng()-0.01}
        ]);
    });
}

function placeGeofencePolygon(points){
    window['{{ $polygonVar ?? 'sitePolygon' }}'] = new google.maps.Polygon({
        paths: points,
        editable: true,
        draggable: true,
        strokeColor: '#007bff',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#007bff',
        fillOpacity: 0.2,
        map: window['{{ $mapVar ?? 'siteMap' }}']
    });

    const centroid = getGeofenceCentroid(points);
    window['{{ $markerVar ?? 'siteMarker' }}'] = new google.maps.Marker({
        position: centroid,
        map: window['{{ $mapVar ?? 'siteMap' }}'],
        draggable: true
    });

    window['{{ $markerVar ?? 'siteMarker' }}'].addListener('drag', ()=>{
        const markerPos = window['{{ $markerVar ?? 'siteMarker' }}'].getPosition();
        const oldCentroid = getGeofenceCentroid(window['{{ $polygonVar ?? 'sitePolygon' }}'].getPath().getArray().map(p=>({lat:p.lat(),lng:p.lng()})));
        const offsetLat = markerPos.lat() - oldCentroid.lat;
        const offsetLng = markerPos.lng() - oldCentroid.lng;

        const newPaths = window['{{ $polygonVar ?? 'sitePolygon' }}'].getPath().getArray().map(p=>({
            lat: p.lat()+offsetLat,
            lng: p.lng()+offsetLng
        }));
        window['{{ $polygonVar ?? 'sitePolygon' }}'].setPath(newPaths);
        updateGeofenceHiddenFields();
    });

    ['set_at','insert_at','remove_at'].forEach(ev=>{
        window['{{ $polygonVar ?? 'sitePolygon' }}'].getPath().addListener(ev, updateGeofenceHiddenFields);
    });
    window['{{ $polygonVar ?? 'sitePolygon' }}'].addListener('drag', updateGeofenceHiddenFields);

    updateGeofenceHiddenFields();
}

function updateGeofenceHiddenFields(){
    if(!window['{{ $polygonVar ?? 'sitePolygon' }}']) return;
    const coords = window['{{ $polygonVar ?? 'sitePolygon' }}'].getPath().getArray().map(p=>({lat:p.lat(),lng:p.lng()}));
    $('#site_geofence').val(JSON.stringify(coords));
    const centroid = getGeofenceCentroid(coords);
    $('#site_lat').val(centroid.lat);
    $('#site_lng').val(centroid.lng);
}

function clearGeofenceShapes(){
    if(window['{{ $polygonVar ?? 'sitePolygon' }}']){
        window['{{ $polygonVar ?? 'sitePolygon' }}'].setMap(null);
        window['{{ $polygonVar ?? 'sitePolygon' }}'] = null;
    }
    if(window['{{ $markerVar ?? 'siteMarker' }}']){
        window['{{ $markerVar ?? 'siteMarker' }}'].setMap(null);
        window['{{ $markerVar ?? 'siteMarker' }}'] = null;
    }
}

function getGeofenceCentroid(coords){
    let lat=0, lng=0;
    coords.forEach(c=>{lat+=c.lat; lng+=c.lng;});
    return {lat: lat/coords.length, lng: lng/coords.length};
}

// Init on modal show
$('#upsertSiteModal').on('shown.bs.modal', initGeofence);
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}" async defer></script>
@endpush
