@extends('layouts.app')

@section('title', 'Customers')

@section('content')
    <div class="mobile-padding">
        <!-- Map -->
        <div class="overlay-card w-100 mb-4" {{ auth()->user()->email == 'pcc-tfl@gmail.com' ? '' : 'hidden' }}>
            <div class="card-title">
                <span>Customers Map</span>
                <small class="text-secondary">Location of registered customers</small>
            </div>
            <div class="alerts-section p-0 overflow-hidden">
                <div id="customers-map" style="width: 100%; height: 500px; border-radius: 0 0 20px 20px;"></div>
            </div>
        </div>

        <!-- Search -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control"
                placeholder="Search by name, email, hotel, country...">
        </div>

        <!-- Customer Cards -->
        <div id="customers-container" class="customers-container"></div>

        <!-- Pagination -->
        <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
    </div>
@endsection

@push('scripts')
    <style>
        .customers-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        @media (min-width: 768px) {
            .customers-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1200px) {
            .customers-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .customer-card {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            backdrop-filter: blur(10px);
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.6);
            animation: slideUp 0.5s ease-out;
        }

        [data-theme="dark"] .customer-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .customer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .customer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .customer-title {
            font-weight: 600;
            color: var(--text-color);
        }

        .customer-hotel {
            font-size: 0.9rem;
            color: var(--gray);
        }
    </style>

    <script>
        let customersData = [];
        let currentPage = 1;
        const perPage = 12;

        let mapApiReady = false;

        function initCustomersMap() {
            mapApiReady = true;
            renderMap();
        }

        async function fetchCustomers(search = '') {
            try {
                const res = await fetch(`{{ route('guests.live') }}?search=${encodeURIComponent(search)}`);
                customersData = await res.json();
                currentPage = 1;
                renderCustomers();
                renderPagination();
                renderMap();
            } catch (e) {
                console.error("Failed to fetch customers", e);
            }
        }

        function renderCustomers() {
            const container = document.getElementById('customers-container');
            container.innerHTML = '';
            const start = (currentPage - 1) * perPage;
            const pageItems = customersData.slice(start, start + perPage);

            if (pageItems.length === 0) {
                container.innerHTML = `
                <div class="customer-card">
                    <div class="customer-header"><div class="customer-title">No customers available</div></div>
                    <div class="customer-details"><p>No registered customers found 🎉</p></div>
                </div>`;
                return;
            }

            pageItems.forEach(customer => {
                const scanCount = customer.scans?.length ?? 0;
                let scanList = '';
                if (scanCount > 0) {
                    scanList = `<ul class="mt-2">` + customer.scans.map(s =>
                            `<li>${_(s.user.name)} @ ${new Date(_(s.created_at)).toLocaleTimeString()}</li>`).join(
                            '') +
                        `</ul>`;
                }

                let reservationEl = '';
                if (customer.reservations.length) {
                    let r = customer.reservations[0];
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
                <div class="customer-card">
                    <div class="customer-header">
                        <div class="customer-title">${_(customer.name ?? '-')}</div>
                        <div class="customer-hotel">${_(customer.profile?.hotel?.name ?? '-')}</div>
                    </div>
                    <div class="customer-details">
                        <p><i class="bi bi-envelope me-1"></i> ${_(customer.email ?? '-')}</p>
                        <p><i class="bi bi-phone me-1"></i> ${_(customer.profile.phone ?? '-')}</p>
                        <p><i class="bi bi-flag me-1"></i> ${_(customer.profile?.country?.name_en ?? '-')}</p>
                        <div> ${scanList} </div>
                        <small class="text-secondary">Joined: ${_(customer.created_at ?? '-')}</small>
                    </div>
                    ${reservationEl} 
                </div>
            `);
            });
        }

        function renderPagination() {
            const totalPages = Math.ceil(customersData.length / perPage);
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
            renderCustomers();
            renderPagination();
        }

        function renderMap() {
            if (!mapApiReady) return;
            const map = new google.maps.Map(document.getElementById('customers-map'), {
                center: {
                    lat: 36.7601,
                    lng: 3.0503
                },
                zoom: 6,
                gestureHandling: "cooperative"
            });
            const bounds = new google.maps.LatLngBounds();

            customersData.forEach(customer => {
                if (customer.profile && customer.profile.lat && customer.profile.lng) {
                    const pos = {
                        lat: Number(customer.profile.lat),
                        lng: Number(customer.profile.lng)
                    };
                    const marker = new google.maps.Marker({
                        position: pos,
                        map,
                        title: _(customer.name),
                        icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                    });
                    const info = new google.maps.InfoWindow({
                        content: `<div class='text-dark' >
                            <strong>${_(customer.name)}</strong><br>${_(customer.email)}<br>
                              Hotel: ${_(customer.profile.hotel?.name ?? '-')}<br>
                              Country: ${_(customer.profile.country?.name_en ?? '-')}<br>
                              <small>${_(customer.created_at)}</small>
                            </div>`
                    });
                    marker.addListener("click", () => info.open(map, marker));
                    bounds.extend(pos);
                }
            });
            if (!bounds.isEmpty()) map.fitBounds(bounds);
        }

        document.getElementById('searchInput').addEventListener('input', e => {
            fetchCustomers(e.target.value);
        });

        fetchCustomers();
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&callback=initCustomersMap" async
        defer></script>
@endpush
