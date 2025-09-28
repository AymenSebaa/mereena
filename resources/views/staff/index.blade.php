@extends('layouts.app')

@section('title', 'Staff Management')
@php
    $role_id = auth()->user()->profile->role_id;
@endphp
@section('content')
    <!-- Staff Map -->
    <div class="overlay-card w-100 mb-4">
        <div class="card-title">
            <span>Staff Map</span>
            <small class="text-secondary">Showing staff locations</small>
        </div>
        <div class="alerts-section p-0 overflow-hidden">
            <div id="staff-map" style="width: 100%; height: 500px; border-radius: 0 0 20px 20px;"></div>
        </div>
    </div>

    <div class="mobile-padding">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <input type="text" id="staff-search" placeholder="Search staff..." class="form-control w-50">

            @if (in_array($role_id, [1, 2]))
                <button class="btn btn-primary" onclick="openStaffModal()">
                    <i class="bi bi-plus-circle me-2"></i> Add Staff
                </button>
            @endif
        </div>

        <div id="staff-container" class="dls-container"></div>

        <div id="staff-pagination" class="mt-3 d-flex justify-content-center gap-2"></div>
    </div>

    <!-- Upsert Modal (Glass Blur) -->
    <div class="modal fade" id="staffModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="staffForm" class="modal-content glass-card">
                @csrf
                <input type="hidden" name="id" id="staff-id">

                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold">Staff Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="staff-name" required>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="staff-email" required>
                        </div>

                        <!-- Country -->
                        <div class="col-md-6">
                            <label class="form-label">Country</label>
                            <select class="form-select" name="country_id" id="staff-country" required>
                                <option value="">-- Select Country --</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name_en }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone" id="staff-phone" required>
                        </div>

                        <!-- Role -->
                        <div class="col-md-4">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role_id" id="staff-role" required>
                                <option value="">-- Select Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Hotel -->
                        <div class="col-md-8 d-none" id="hotel-wrapper">
                            <label class="form-label">Hotel</label>
                            <select class="form-select" name="hotel_id" id="staff-hotel">
                                <option value="">-- Select Hotel --</option>
                                @foreach ($hotels as $hotel)
                                    <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Zone -->
                        <div class="col-md-8 d-none" id="zone-wrapper">
                            <label class="form-label">Zone</label>
                            <select class="form-select" name="zone_id" id="staff-zone">
                                <option value="">-- Select Zone --</option>
                                @foreach ($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const staffModal = new bootstrap.Modal(document.getElementById('staffModal'));

            const hotelWrapper = document.getElementById('hotel-wrapper');
            const zoneWrapper = document.getElementById('zone-wrapper');
            const roleSelect = document.getElementById('staff-role');

            function toggleHotelZone(roleId) {
                // hide both by default
                hotelWrapper.classList.add('d-none');
                zoneWrapper.classList.add('d-none');

                // Show based on role
                if (roleId == 3 || roleId == 6) { // Supervisor or Despatcher
                    zoneWrapper.classList.remove('d-none');
                } else if (roleId == 4) { // Operator
                    hotelWrapper.classList.remove('d-none');
                }
            }

            roleSelect.addEventListener('change', function() {
                toggleHotelZone(this.value);
            });

            window.openStaffModal = function() {
                const form = document.getElementById('staffForm');
                form.reset();
                document.getElementById('staff-id').value = '';
                toggleHotelZone(roleSelect.value);
                staffModal.show();
            };


            window.editStaff = function(user) {
                if (typeof user === 'string') {
                    try {
                        user = JSON.parse(user);
                    } catch (e) {
                        return;
                    }
                }

                document.getElementById('staff-id').value = user.id ?? '';
                document.getElementById('staff-name').value = user.name ?? '';
                document.getElementById('staff-email').value = user.email ?? '';
                document.getElementById('staff-phone').value = user.profile?.phone ?? '';
                document.getElementById('staff-role').value = user.profile?.role_id ?? '';
                document.getElementById('staff-country').value = user.profile?.country_id ?? '';
                document.getElementById('staff-hotel').value = user.profile?.hotel_id ?? '';
                document.getElementById('staff-zone').value = user.profile?.zone_id ?? '';
                toggleHotelZone(user.profile?.role_id);
                staffModal.show();
            };

            document.getElementById('staffForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const fd = new FormData(this);
                fetch("{{ route('staff.upsert') }}", {
                        method: 'POST',
                        body: fd,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(r => r.json())
                    .then(json => {
                        if (json.success) {
                            staffModal.hide();
                            setTimeout(() => location.reload(), 450);
                        } else alert(json.message || 'Failed to save staff');
                    }).catch(err => {
                        console.error(err);
                        alert('Error saving staff');
                    });
            });

            window.deleteStaff = function(id) {
                if (!confirm('Are you sure you want to delete this user?')) return;
                fetch(`/staff/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(r => r.json()).then(json => {
                    if (json.success) location.reload();
                    else alert(json.message || 'Failed to delete');
                }).catch(err => {
                    console.error(err);
                    alert('Failed to delete user');
                });
            };
        });

        // Inject staff data as JSON
        const staffData = @json($staff);
        const staff = staffData.data;

        const staffPageSize = 20;
        let staffCurrentPage = 1;

        function renderStaffPage(page = 1, search = '') {
            staffCurrentPage = page;

            const filtered = staff.filter(user =>
                user.name.toLowerCase().includes(search.toLowerCase()) ||
                user.email.toLowerCase().includes(search.toLowerCase())
            );

            const start = (page - 1) * staffPageSize;
            const end = start + staffPageSize;
            const pageStaff = filtered.slice(start, end);

            const container = document.getElementById('staff-container');
            container.innerHTML = '';

            pageStaff.forEach(user => {
                container.innerHTML += `
                    <div class="dl-card" data-id="${_(user.id)}">
                        <div class="dl-header">
                            <div class="d-flex align-items-center">
                                <div class="avatar" title="${user.name}">
                                    ${(_(user.name?.charAt(0).toUpperCase() || 'U'))}
                                </div>
                                <div class="staff-meta ms-3">
                                    <div class="staff-name">${_(user.name)}</div>
                                    <div class="staff-email text-secondary"> 
                                        <i class='bi bi-envelope me-1' ></i> ${_(user.email)} 
                                    </div>
                                    <div class="staff-email text-secondary"> 
                                        <i class='bi bi-phone me-1' ></i> ${_(user.profile?.phone ?? '-')}
                                    </div>
                                </div>
                            </div>
                            @if (in_array($role_id, [1, 2]))
                            <div class="dl-actions">
                                <button class="icon-btn edit" onclick='editStaff(${JSON.stringify(user)})'>
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="icon-btn delete" onclick="deleteStaff(${user.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            @endif
                        </div>

                        <div class="dl-badges mt-3">
                            <span class="badge role-badge">
                                <i class="bi bi-people me-1"></i> ${_(user.profile?.role?.name ?? '-')}
                            </span>
                            <span class="badge country-badge">
                                <i class="bi bi-globe me-1"></i> ${_(user.profile?.country?.name_en ?? '-')}
                            </span>
                            ${user.profile?.hotel ? `<span class="badge hotel-badge"><i class="bi bi-building me-1"></i> ${_(user.profile.hotel.name)}</span>` : ''}
                            ${user.profile?.zone ? `<span class="badge zone-badge"><i class="bi bi-geo-alt me-1"></i> ${_(user.profile.zone.name)}</span>` : ''}
                        </div>

                        <small class="badge mt-3">
                           <i class='bi bi-key me-1' ></i> OTP: ${_(user.lastOtp?.code ?? 'N/A')} | EXP: ${_(user.lastOtp?.expires_at ?? '-')}
                        </small>

                        <div class="dl-footer mt-2 text-secondary small">
                            Joined: ${_(user.created_at ? user.created_at.substring(0,10) : '-')}
                        </div>
                    </div>`;
            });

            // Pagination
            const totalPages = Math.ceil(filtered.length / staffPageSize);
            const pagination = document.getElementById('staff-pagination');
            pagination.innerHTML = '';

            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('span');
                btn.className = `pagination-btn ${i===page ? 'active' : ''}`;
                btn.textContent = i;
                btn.onclick = () => renderStaffPage(i, search);
                pagination.appendChild(btn);
            }
        }

        // Search input
        document.getElementById('staff-search').addEventListener('input', e => {
            renderStaffPage(1, e.target.value);
        });

        // Initial load
        renderStaffPage();

        let staffMapApiReady = false;
        let staffMarkers = []; // global array to keep markers and info windows
        let staffMap; // global map instance

        function initStaffMap() {
            staffMapApiReady = true;

            staffMap = new google.maps.Map(document.getElementById('staff-map'), {
                center: {
                    lat: 36.7601,
                    lng: 3.0503
                },
                zoom: 6,
                gestureHandling: "cooperative",
            });

            const bounds = new google.maps.LatLngBounds();

            staff.forEach(user => {
                const {
                    profile
                } = user;
                const {
                    lat,
                    lng,
                    role,
                    hotel,
                    zone,
                    country
                } = profile;
                if (!lat || !lng) return;

                const pos = {
                    lat: parseFloat(lat),
                    lng: parseFloat(lng)
                };

                const marker = new google.maps.Marker({
                    position: pos,
                    map: staffMap,
                    title: user.name,
                    icon: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                });

                const info = new google.maps.InfoWindow({
                    content: `
                            <div class='text-dark'>
                                <strong>${_(user.name)}</strong><br>
                                Role: ${_(role.name)}<br>
                                Hotel: ${_(hotel?.name ?? zone?.name)}<br>
                                Country: ${_(country.name)}
                            </div>`
                });

                marker.addListener('click', () => info.open(staffMap, marker));

                staffMarkers.push({
                    id: user.id,
                    marker,
                    info
                });
                bounds.extend(pos);
            });

            if (!bounds.isEmpty()) staffMap.fitBounds(bounds);
        }

        function renderStaffMap() {
            if (!staffMapApiReady) return;

            staffMap = new google.maps.Map(document.getElementById('staff-map'), {
                center: {
                    lat: 36.7601,
                    lng: 3.0503
                },
                zoom: 6,
                gestureHandling: "cooperative"
            });
        }

        // Staff card click listeners
        document.querySelectorAll('.staff-card').forEach(card => {
            card.addEventListener('click', () => {
                const staffId = parseInt(card.dataset.id);
                const staff = staffMarkers.find(s => s.id === staffId);

                if (staff) {
                    staffMap.panTo(staff.marker.getPosition());
                    staffMap.setZoom(14); // optional zoom
                    staff.info.open(staffMap, staff.marker);

                    // Bounce animation
                    staff.marker.setAnimation(google.maps.Animation.BOUNCE);
                    setTimeout(() => staff.marker.setAnimation(null), 1400);
                }
            });
        });
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&callback=initStaffMap" async
        defer></script>
@endpush

