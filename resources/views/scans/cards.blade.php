<!-- Search + Cards + Pagination Container -->
<div id="cards-wrapper">
    <div class="mb-4">
        <!-- Search -->
        <div class="d-flex align-items-center mb-3">
            <input type="text" id="scan-search" placeholder="Search scans..." class="form-control w-50 me-2">
        </div>

        <!-- Cards -->
        <div id="scans-container" class="scans-container"></div>

        <!-- Pagination -->
        <div id="pagination" class="d-flex justify-content-center mt-4"></div>
    </div>
</div>

<script>
    // Haversine formula (km)
    function calcDistance(lat1, lon1, lat2, lon2) {
        if (!lat1 || !lon1 || !lat2 || !lon2) return null;
        const R = 6371; // radius of Earth in km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) *
            Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return (R * c).toFixed(2); // km
    }

    // Render cards
    function renderPage(page = 1, search = '') {
        currentPage = page;
        const filtered = allScans.filter(s =>
            (s.user?.name ?? '').toLowerCase().includes(search.toLowerCase()) ||
            (s.scanable?.name ?? '').toLowerCase().includes(search.toLowerCase()) ||
            (s.user?.profile?.hotel?.name ?? '').toLowerCase().includes(search.toLowerCase())
        );

        const start = (page - 1) * pageSize;
        const end = start + pageSize;
        const pageScans = filtered.slice(start, end);

        const container = $('#scans-container');
        container.empty();

        pageScans.forEach(scan => {
            let icon = '',
                name = '';
            switch (scan.type) {
                case 'buses':
                    icon = 'bi bi-bus-front';
                    name = scan.bus?.name ?? scan.content?.name ?? '';
                    break;
                case 'hotels':
                    icon = 'bi bi-building';
                    name = scan.hotel?.name ?? '';
                    break;
                case 'users':
                    icon = 'bi bi-people';
                    name = scan.guest?.name ?? '';
                    break;
            }

            // Hotel + Distance
            const hotel = scan.user?.profile?.hotel?.name ?? '-';
            const hotelLat = parseFloat(scan.user?.profile?.hotel?.lat);
            const hotelLng = parseFloat(scan.user?.profile?.hotel?.lng);
            const dist = calcDistance(parseFloat(scan.lat), parseFloat(scan.lng), hotelLat, hotelLng);

            const formatDate = (date) => new Date(_(date)).toLocaleString('fr-FR');

            const html = `
                <div class="scan-card" data-lat="${_(scan.lat)}" data-lng="${_(scan.lng)}">
                    <div class='my-2 d-flex justify-content-between align-items-center'>
                        <span><i class='${icon} me-2'></i> ${_(name)}</span>
                        <small class='badge-extra ${scan.extra ?? 'none'}'>${scan.extra ?? ' '}</small>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <div><strong>User:</strong> ${_(scan.user?.name ?? '-')} (${_(scan.user?.profile?.role?.name ?? '-')})</div>
                        <div><strong>Hotel:</strong> ${hotel}</div>
                        ${dist ? `<div><strong>Distance:</strong> ${dist} km</div>` : ''}
                    </div>
                    <div class='d-flex justify-content-between align-items-center'>
                        <small>${formatDate(_(scan.created_at))}</small>
                        <button class="btn btn-sm btn-light mt-2 scan-item" data-lat="${_(scan.lat)}" data-lng="${_(scan.lng)}">Show on map</button>
                    </div>
                </div>`;
            container.append(html);
        });

        // Pagination (same as before)...
        const totalPages = Math.ceil(filtered.length / pageSize);
        const pagination = $('#pagination');
        pagination.empty();
        for (let i = 1; i <= totalPages; i++) {
            const btn = $(`<span class="pagination-btn ${i===page?'active':''}">${i}</span>`);
            btn.on('click', () => renderPage(i, search));
            pagination.append(btn);
        }
    }
</script>
