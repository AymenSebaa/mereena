@extends('layouts.app')

@section('title', 'Guests')

@section('content')
    <div class="mobile-padding">
        <!-- Map -->
        <div class="overlay-card w-100 mb-4" {{ auth()->user()->email == 'pcc-tfl@gmail.com' ? '' : 'hidden' }}>
            <div class="card-title">
                <span>Guests Map</span>
                <small class="text-secondary">Location of registered guests</small>
            </div>
            <div class="alerts-section p-0 overflow-hidden">
                <div id="guests-map" style="width: 100%; height: 500px; border-radius: 0 0 20px 20px;"></div>
            </div>
        </div>

        <!-- Search -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control"
                placeholder="Search by name, email, hotel, country...">
        </div>

        <!-- Guest Cards -->
        <div id="guests-container" class="guests-container"></div>

        <!-- Pagination -->
        <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
    </div>
@endsection

@push('scripts')
    <style>
        .guests-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        @media (min-width: 768px) {
            .guests-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1200px) {
            .guests-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .guest-card {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            backdrop-filter: blur(10px);
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.6);
            animation: slideUp 0.5s ease-out;
        }

        [data-theme="dark"] .guest-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .guest-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .guest-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .guest-title {
            font-weight: 600;
            color: var(--text-color);
        }

        .guest-hotel {
            font-size: 0.9rem;
            color: var(--gray);
        }
    </style>

    <script>
        let guestsData = [];
        let currentPage = 1;
        const perPage = 12;

        let mapApiReady = false;

        function initGuestsMap() {
            mapApiReady = true;
            renderMap();
        }

        async function fetchGuests(search = '') {
            try {
                const res = await fetch(`{{ oRoute('guests.live') }}?search=${encodeURIComponent(search)}`);
                guestsData = await res.json();
                currentPage = 1;
                renderGuests();
                renderPagination();
                renderMap();
            } catch (e) {
                console.error("Failed to fetch guests", e);
            }
        }

        function renderGuests() {
            const container = document.getElementById('guests-container');
            container.innerHTML = '';
            const start = (currentPage - 1) * perPage;
            const pageItems = guestsData.slice(start, start + perPage);

            if (pageItems.length === 0) {
                container.innerHTML = `
                <div class="guest-card">
                    <div class="guest-header"><div class="guest-title">No guests available</div></div>
                    <div class="guest-details"><p>No registered guests found 🎉</p></div>
                </div>`;
                return;
            }

            pageItems.forEach(guest => {
                const scanCount = guest.scans?.length ?? 0;
                let scanList = '';
                if (scanCount > 0) {
                    scanList = `<ul class="mt-2">` + guest.scans.map(s =>
                            `<li>${_(s.user.name)} @ ${new Date(_(s.created_at)).toLocaleTimeString()}</li>`).join(
                            '') +
                        `</ul>`;
                }

                let reservationEl = '';
                if (guest.reservations.length) {
                    let r = guest.reservations[0];
                    const flight = r ? typeof r.content === 'string' ? JSON.parse(r.content) : r.content : null;

                    let statusBadge = 'status-pending';
                    switch (r.status.name) {
                        case 'In Progress':
                            statusBadge = 'status-pending';
                            break;
                        case 'Approved':
                            statusBadge = 'status-online';
                            break;
                        case 'Rejected':
                            statusBadge = 'status-offline';
                            break;
                        default:
                            statusBadge = 'status-pending';
                    }

                    reservationEl = `
                        <div class="card" data-id="${r.id}">
                            <div class="card-header small d-flex justify-content-between mb-2">
                                <strong class='me-2'><i class='bi bi-alarm me-1'></i>  ${formatDateTime(_(r.pickup_time ?? '-'))}</strong>
                                <span class='departure-status ${statusBadge}'>${_(r.status.name)}</span>
                            </div>
                            <div class="card-body py-0 small mb-1">
                                ${r.status.name == 'Rejected' && r.note ? `<br> <i class="bi bi-exclamation-triangle-fill text-danger"></i> <span class='text-danger ms-2' >${r.note}</span>`:'' }
                                <i class='bi bi-airplane me-1'></i> ${_(flight.flightNumber ?? 'N/A')} | ${flight.departureOrArrival === 'departure' ? `<i class="bi bi-globe-europe-africa me-1"></i> ${_(flight.arrivalAirport?.city ?? '-')} (${_(flight.arrivalAirport?.code ?? '-')})` : `<strong>Origin:</strong> ${_(flight.departureAirport?.city ?? '-')} (${_(flight.departureAirport?.code ?? '-')})`}<br>
                                <i class='bi bi-calendar me-1'></i> ${_(flight.operationTime?.date ?? '-')} | <i class='bi bi-clock me-1' ></i> ${_(flight.operationTime?.time ?? '-')}<br>
                            </div>
                        </div>
                    `;
                }



                container.insertAdjacentHTML('beforeend', `
                <div class="guest-card">
                    <div class="guest-header">
                        <div class="guest-title">${_(guest.name ?? '-')}</div>
                        <div class="guest-hotel">${_(guest.profile?.hotel?.name ?? '-')}</div>
                    </div>
                    <div class="guest-details">
                        <p><i class="bi bi-envelope me-1"></i> ${_(guest.email ?? '-')}</p>
                        <p><i class="bi bi-phone me-1"></i> ${_(guest.profile.phone ?? '-')}</p>
                        <p><i class="bi bi-flag me-1"></i> ${_(guest.profile?.country?.name_en ?? '-')}</p>
                        <div> ${scanList} </div>
                        <small class="text-secondary">Joined: ${_(guest.created_at ?? '-')}</small>
                    </div>
                    ${reservationEl} 
                </div>
            `);
            });
        }

        function renderPagination() {
            const totalPages = Math.ceil(guestsData.length / perPage);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';
            if (totalPages <= 1) return;

            for (let i = 1; i <= totalPages; i++) {
                pagination.insertAdjacentHTML('beforeend', `
                <button class="btn btn-sm ${i===currentPage?'btn-primary':'btn-light'} mx-1"
                        onclick="goToPage(${i})">${i}</button>`);
            }
        }

        function goToPage(page) {
            currentPage = page;
            renderGuests();
            renderPagination();
        }

        function renderMap() {
            if (!mapApiReady) return;
            const map = new google.maps.Map(document.getElementById('guests-map'), {
                center: {
                    lat: 36.7601,
                    lng: 3.0503
                },
                zoom: 6,
                gestureHandling: "cooperative"
            });
            const bounds = new google.maps.LatLngBounds();

            guestsData.forEach(guest => {
                if (guest.profile && guest.profile.lat && guest.profile.lng) {
                    const pos = {
                        lat: Number(guest.profile.lat),
                        lng: Number(guest.profile.lng)
                    };
                    const marker = new google.maps.Marker({
                        position: pos,
                        map,
                        title: _(guest.name),
                        icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                    });
                    const info = new google.maps.InfoWindow({
                        content: `<div class='text-dark' >
                            <strong>${_(guest.name)}</strong><br>${_(guest.email)}<br>
                              Hotel: ${_(guest.profile.hotel?.name ?? '-')}<br>
                              Country: ${_(guest.profile.country?.name_en ?? '-')}<br>
                              <small>${_(guest.created_at)}</small>
                            </div>`
                    });
                    marker.addListener("click", () => info.open(map, marker));
                    bounds.extend(pos);
                }
            });
            if (!bounds.isEmpty()) map.fitBounds(bounds);
        }

        document.getElementById('searchInput').addEventListener('input', e => {
            fetchGuests(e.target.value);
        });

        fetchGuests();
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&callback=initGuestsMap" async
        defer></script>
@endpush
