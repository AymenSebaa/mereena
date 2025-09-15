<!-- Table -->
<div id="scans-table-container" style="display:none;">
    <table id="scans-table" class="table table-striped table-bordered w-100">
        <thead>
            <tr>
                <th>Zone</th>
                <th>Hotel</th>
                <th>km</th>
                <th>Operator</th>
                <th>Name</th>
                <th>Action</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>


<script>
    let isTableView = false;
    let dataTable = null;

    // extra globals for visualization
    let tempHotelMarker = null;
    let tempLine = null;
    let mapCentered = false;

    // Render table
    function renderTable(scans) {
        if (!dataTable) return;

        dataTable.clear();
        scans.forEach(scan => {
            const { id, user } = scan;
            const { profile } = user;
            const { hotel, role } = profile;

            let content = {};
            try {
                if (typeof scan.content === 'string' && scan.content.trim() !== '') {
                    content = JSON.parse(scan.content);
                } else if (typeof scan.content === 'object' && scan.content !== null) {
                    content = scan.content; // already object
                }
            } catch (e) {
                console.warn('Invalid JSON in scan.content', scan.content, e);
            }

            const zoneEl =  `<span class='text-nowrap'>${_(hotel?.zones[0]?.name ?? '-')}</span>`;
            const hotelEl = `<span class='text-nowrap'>${_(hotel?.name ?? '-')}</span>`;
            const distEl = `<span>${calcDistance(scan.lat, scan.lng, hotel.lat, hotel.lng) ?? '-'}</span>`;
            const userEl = `<span class='text-nowrap'>${_(user.name ?? '-')}</span>`;
            const nameEl = `<span class='text-nowrap'>${_(content?.name ?? '')}</span>`;
            const extraEl = `<span class="badge-extra ${scan.extra ?? 'none'}">${scan.extra ?? ''}</span>`;
            const createdAtEl = `<span class='text-nowrap'>${formatDateTime(scan.created_at)}</span>`;

            const rowNode = dataTable.row.add([
                zoneEl,
                hotelEl,
                distEl,
                userEl,
                nameEl,
                extraEl,
                createdAtEl
            ]).draw(false).node();

            // attach scan id for row -> marker lookup
            $(rowNode).attr('data-scan-id', id);
        });

        // Add row click listener
        $('#scans-table tbody').off('click').on('click', 'tr', function() {
            const scanId = $(this).attr('data-scan-id');
            if (!scanId) return;

            const scan = allScans.find(s => s.id == scanId);
            if (!scan || !scan.lat || !scan.lng) return;

            const pos = {
                lat: parseFloat(scan.lat),
                lng: parseFloat(scan.lng)
            };

            // --- Hotel visualization ---
            const hLat = parseFloat(scan.user?.profile?.hotel?.lat);
            const hLng = parseFloat(scan.user?.profile?.hotel?.lng);

            // clear old temp visuals
            if (tempHotelMarker) tempHotelMarker.setMap(null);
            if (tempLine) tempLine.setMap(null);

            if (hLat && hLng) {
                const hotelPos = {
                    lat: hLat,
                    lng: hLng
                };

                // temp hotel marker
                tempHotelMarker = new google.maps.Marker({
                    position: hotelPos,
                    map,
                    title: scan.user?.profile?.hotel?.name ?? 'Hotel',
                    icon: {
                        url: "https://maps.google.com/mapfiles/kml/shapes/lodging.png",
                        scaledSize: new google.maps.Size(32, 32)
                    }
                });

                // line between hotel and scan
                tempLine = new google.maps.Polyline({
                    path: [hotelPos, pos],
                    geodesic: true,
                    strokeColor: "#ff0000",
                    strokeOpacity: 0.7,
                    strokeWeight: 2,
                    map: map
                });
            }

            // --- Center and zoom map ---
            map.panTo(pos);
            map.setZoom(15);

            // Scroll page back to map
            document.getElementById('scan-map').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            // OR with jQuery animation:
            // $('html, body').animate({ scrollTop: $('#scan-map').offset().top - 20 }, 600);

            // Find the marker
            const marker = scanMarkers.find(m =>
                m.getPosition().lat() === pos.lat &&
                m.getPosition().lng() === pos.lng
            );
            if (marker) {
                marker.setAnimation(google.maps.Animation.BOUNCE);
                setTimeout(() => marker.setAnimation(null), 1400);
                google.maps.event.trigger(marker, 'click');
            }
        });

    }

    // modified renderMarkers to avoid recentering after first time
    function renderMarkers(scans) {
        scanMarkers.forEach(s => s.setMap(null));
        scanMarkers = [];
        bounds = new google.maps.LatLngBounds();

        scans.forEach(scan => {
            const { user } = scan;
            const { profile } = user;
            const { role, hotel } = profile;
            
            if (!scan.lat || !scan.lng) return;
            const pos = {
                lat: parseFloat(scan.lat),
                lng: parseFloat(scan.lng)
            };

            const extra = scan.extra || 'none';
            const markerColor = colorMap[extra] || colorMap['none'];

            const marker = new google.maps.Marker({
                position: pos,
                map,
                title: _(user?.name ?? 'Deleted User'),
                icon: getCustomMarker(markerColor)
            });

            const infoWindow = new google.maps.InfoWindow({
                content: `<div class='text-dark'>
                    <strong>${_(user?.name ?? '-')}</strong> (${_(role?.name ?? '-')})<br>
                    Hotel: ${_(hotel?.name ?? '-') }<br>
                    Distance: ${
                        hotel?.lat && hotel?.lng ? calcDistance(scan.lat, scan.lng, hotel.lat, hotel.lng) + ' km' : '-'
                    }<br>
                    Type: ${_(scan.type)}<br>
                    Name: ${_(scan.bus?.name ?? scan.hotel?.name ?? scan.guest?.name ?? '')}<br>
                    At: ${new Date(_(scan.created_at)).toLocaleString()}
                </div>`
            });

            marker.addListener('click', () => infoWindow.open(map, marker));
            scanMarkers.push(marker);
            bounds.extend(pos);
        });

        // only center once
        if (!mapCentered && !bounds.isEmpty()) {
            map.fitBounds(bounds);
            mapCentered = true;
        }
    }
</script>
