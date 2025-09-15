@extends('layouts.app')

@section('title', 'Reservations')

@php
    $role_id = auth()->user()->profile->role_id;
    $permissions = auth()->user()->profile->role->permissions ?? [];
@endphp
@section('content')
    <div class="mobile-padding">

        <!-- Search -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <input type="text" id="reservationSearch" class="form-control"
                placeholder="Search by name, flight, or status...">

            @if ($role_id == 10)
                @include('reservations.create-modal')
            @endif

            @if (false && auth()->user()->email == 'k.samadi@transtev.dz')
                @include('reservations.import-modal')
            @endif
        </div>

        <!-- Reservations Grid -->
        <div id="reservations-container" class="tasks-container"></div>

        <!-- Pagination -->
        <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
    </div>

    <!-- Reject Modal -->
    @include('reservations.reject-modal')

@endsection

@push('scripts')
    <script>
        let reservationsData = [];
        let currentPage = 1;
        const perPage = 9;

        async function fetchReservations(search = '') {
            try {
                const res = await fetch(`{{ route('reservations.live') }}?search=${encodeURIComponent(search)}`);
                const data = await res.json();
                reservationsData = data;
                currentPage = 1;
                renderReservations();
                renderPagination();
            } catch (err) {
                console.error("Failed to load reservations:", err);
            }
        }

        function renderReservations() {
            const container = document.getElementById('reservations-container');
            container.innerHTML = '';

            const start = (currentPage - 1) * perPage;
            const pageItems = reservationsData.slice(start, start + perPage);

            if (pageItems.length === 0) {
                container.innerHTML = `<div class="task-card">No reservations found</div>`;
                return;
            }

            pageItems.forEach(r => {
                const flight = typeof r.content === 'string' ? JSON.parse(r.content) : r.content;

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

                container.insertAdjacentHTML('beforeend', `
                    <div class="card task-card flight-card" data-id="${r.id}">
                        <div class="card-header d-flex justify-content-between mb-2">
                            <div class='d-flex align-items-center'>
                                <strong class='me-2'>${_(flight.airline?.text ?? 'Unknown Airline')}</strong>
                                <span class="badge bg-${_(flight.codeContext?.color ?? 'secondary')}">${_(flight.codeContext?.text ?? 'N/A')}</span>
                            </div>
                            <span class='departure-status ${statusBadge}'>${_(r.status.name)}</span>
                        </div>
                        <div class="card-body small mb-1">
                            @if (in_array($role_id, [1, 2, 6]))
                            <i class='bi bi-person-fill me-1'></i> ${_(r.user.name ?? '-')}<br>
                            <i class='bi bi-envelope me-1'></i> ${_(r.user.email ?? '-')}<br> 
                            @endif
                            <i class='bi bi-building me-1'></i> ${_(r.user.profile.hotel.name ?? '-')} <br>
                            <i class='bi bi-alarm me-1'></i> ${formatDateTime(_(r.pickup_time ?? '-'))} <br> <br>

                            <i class='bi bi-airplane me-1'></i> ${_(flight.flightNumber ?? 'N/A')} | ${flight.departureOrArrival === 'departure' ? `<i class="bi bi-globe-europe-africa me-1"></i> ${_(flight.arrivalAirport?.city ?? '-')} (${_(flight.arrivalAirport?.code ?? '-')})` : `<strong>Origin:</strong> ${_(flight.departureAirport?.city ?? '-')} (${_(flight.departureAirport?.code ?? '-')})`}<br>
                            <i class='bi bi-calendar me-1'></i> ${_(flight.operationTime?.date ?? '-')} | <i class='bi bi-clock me-1' ></i> ${_(flight.operationTime?.time ?? '-')}<br>

                            ${r.status.name == 'Rejected' && r.note ? `<br> <i class="bi bi-exclamation-triangle-fill text-danger"></i> <span class='text-danger ms-2' >${r.note}</span>`:'' }
                        </div>
                        @if (in_array($role_id, [1, 2, 6]))
                        <div class='card-footer d-flex justify-content-between gap-2 mt-2'>
                            <div>
                                ${ r.editor ? `Edidted by: ${_(r.editor.name)} at ${formatDateTime(r.editor.edited_at)}` : ''}
                            </div>
                            <div>
                                ${ r.status.name != 'Rejected' ? `<button type="button" class="btn btn-danger btn-sm reject-btn">Reject</button>`:''}
                                ${ r.status.name != 'Approved' ? `<button type="button" class="btn btn-success btn-sm approve-btn">Approve</button>`:''}
                            </div>
                        </div>
                        @endif
                    </div>
                `);
            });

            // Attach event listeners after rendering
            document.querySelectorAll('.approve-btn').forEach(btn => {
                btn.addEventListener('click', e => {
                    const id = e.target.closest('.flight-card').dataset.id;
                    approveReservation(id);
                });
            });

            document.querySelectorAll('.reject-btn').forEach(btn => {
                btn.addEventListener('click', e => {
                    const id = e.target.closest('.flight-card').dataset.id;
                    const reservation = reservationsData.find(r => r.id == id);
                    document.getElementById('rejectReservationId').value = id;
                    document.getElementById('rejectReason').value = reservation.note ?? '';
                    new bootstrap.Modal(document.getElementById('rejectModal')).show();
                });
            });
        }

        // Approve function
        @if (in_array('reservations.approve', $permissions))
            async function approveReservation(id) {
                try {
                    const res = await fetch(`{{ route('reservations.approve', ':id') }}`.replace(':id', id), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) fetchReservations();
                } catch (err) {
                    console.error(err);
                }
            }
        @endif

        function statusColor(status) {
            switch ((status || '').toLowerCase()) {
                case 'pending':
                    return 'warning';
                case 'approved':
                    return 'success';
                case 'rejected':
                    return 'danger';
                default:
                    return 'secondary';
            }
        }

        function renderPagination() {
            const totalPages = Math.ceil(reservationsData.length / perPage);
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
            renderReservations();
            renderPagination();
        }

        // Search
        document.getElementById('reservationSearch').addEventListener('input', e => {
            fetchReservations(e.target.value);
        });

        // Init
        fetchReservations();
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
            padding: 8px;
            box-shadow: var(--card-shadow);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            transition: all 0.3s;
        }

        [data-theme="dark"] .task-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f1f5f9;
        }

        .task-card:hover {
            transform: translateY(-5px);
        }
    </style>
@endpush
