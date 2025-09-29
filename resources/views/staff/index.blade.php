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

        <div id="staff-container" class="staff-container"></div>

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

                        <!-- Site -->
                        <div class="col-md-8 d-none" id="site-wrapper">
                            <label class="form-label">Site</label>
                            <select class="form-select" name="site_id" id="staff-site">
                                <option value="">-- Select Site --</option>
                                @foreach ($sites as $site)
                                    <option value="{{ $site->id }}">{{ $site->name }}</option>
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

            const siteWrapper = document.getElementById('site-wrapper');
            const zoneWrapper = document.getElementById('zone-wrapper');
            const roleSelect = document.getElementById('staff-role');

            function toggleSiteZone(roleId) {
                // hide both by default
                siteWrapper.classList.add('d-none');
                zoneWrapper.classList.add('d-none');

                // Show based on role
                if (roleId == 3 || roleId == 6) { // Supervisor or Despatcher
                    zoneWrapper.classList.remove('d-none');
                } else if (roleId == 4) { // Operator
                    siteWrapper.classList.remove('d-none');
                }
            }

            roleSelect.addEventListener('change', function() {
                toggleSiteZone(this.value);
            });

            window.openStaffModal = function() {
                const form = document.getElementById('staffForm');
                form.reset();
                document.getElementById('staff-id').value = '';
                toggleSiteZone(roleSelect.value);
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
                document.getElementById('staff-site').value = user.profile?.site_id ?? '';
                document.getElementById('staff-zone').value = user.profile?.zone_id ?? '';
                toggleSiteZone(user.profile?.role_id);
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
                    <div class="staff-card" data-id="${_(user.id)}">
                        <div class="staff-top">
                            <div class="avatar" title="${user.name}">
                                ${(_(user.name?.charAt(0).toUpperCase() || 'U'))}
                            </div>
                            <div class="staff-meta">
                                <div class="staff-name">${_(user.name)}</div>
                                <div class="staff-email text-secondary"> 
                                    <i class='bi bi-envelope me-1' ></i> ${_(user.email)} 
                                </div>
                                <div class="staff-email text-secondary"> 
                                    <i class='bi bi-phone me-1' ></i> ${_(user.profile?.phone ?? '-')}
                                </div>
                            </div>
                            @if (in_array($role_id, [1, 2]))
                            <div class="staff-actions">
                                <button class="icon-btn edit" onclick='editStaff(${JSON.stringify(user)})'>
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="icon-btn delete" onclick="deleteStaff(${user.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            @endif
                        </div>

                        <div class="staff-badges mt-3">
                            <span class="badge role-badge">
                                <i class="bi bi-people me-1"></i> ${_(user.profile?.role?.name ?? '-')}
                            </span>
                            <span class="badge country-badge">
                                <i class="bi bi-globe me-1"></i> ${_(user.profile?.country?.name_en ?? '-')}
                            </span>
                            ${user.profile?.site ? `<span class="badge site-badge"><i class="bi bi-building me-1"></i> ${_(user.profile.site.name)}</span>` : ''}
                            ${user.profile?.zone ? `<span class="badge zone-badge"><i class="bi bi-geo-alt me-1"></i> ${_(user.profile.zone.name)}</span>` : ''}
                        </div>

                        <small class="badge mt-3">
                           <i class='bi bi-key me-1' ></i> OTP: ${_(user.lastOtp?.code ?? 'N/A')} | EXP: ${_(user.lastOtp?.expires_at ?? '-')}
                        </small>

                        <div class="staff-footer mt-2 text-secondary small">
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
                    site,
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
                                Site: ${_(site?.name ?? zone?.name)}<br>
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

@push('scripts')
    <style>
        /* Glass modal effect */
        .modal-content.glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
        }

        [data-theme="dark"] .modal-content.glass-card {
            background: rgba(17, 24, 39, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-content.glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }

        [data-theme="dark"] .modal-content.glass-card {
            background: rgba(17, 24, 39, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* STAFF CARDS - glassy & polished */
        .staff-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 20px;
        }

        .staff-card {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.85), rgba(255, 255, 255, 0.75));
            border-radius: 14px;
            padding: 16px;
            box-shadow: 0 8px 24px rgba(16, 24, 40, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.6);
            transition: transform .18s ease, box-shadow .18s ease;
            position: relative;
            overflow: hidden;
        }

        [data-theme="dark"] .staff-card {
            background: linear-gradient(180deg, rgba(23, 31, 45, 0.85), rgba(23, 31, 45, 0.75));
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
        }

        .staff-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(2, 6, 23, 0.12);
        }

        .staff-top {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #6ee7b7, #3b82f6);
            box-shadow: 0 6px 18px rgba(59, 130, 246, 0.15);
            flex-shrink: 0;
            font-size: 20px;
        }

        [data-theme="dark"] .avatar {
            background: linear-gradient(135deg, #16a34a, #2563eb);
        }

        .staff-meta {
            flex: 1;
            min-width: 0;
        }

        .staff-name {
            font-weight: 600;
            font-size: 16px;
        }

        .staff-email {
            font-size: 13px;
        }

        .staff-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .icon-btn {
            border: none;
            background: rgba(0, 0, 0, 0.04);
            color: var(--text-color);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background .12s ease, transform .12s ease;
        }

        .icon-btn:hover {
            transform: translateY(-2px);
            background: rgba(0, 0, 0, 0.06);
        }

        .icon-btn.edit {
            color: #d97706;
            background: rgba(245, 158, 11, 0.06);
        }

        .icon-btn.delete {
            color: #dc2626;
            background: rgba(239, 68, 68, 0.06);
        }

        .staff-badges .badge {
            display: inline-block;
            margin-right: 8px;
            margin-bottom: 4px;
            padding: 6px 8px;
            border-radius: 10px;
            font-size: 12px;
            background: rgba(0, 0, 0, 0.04);
            color: var(--text-color);
        }

        [data-theme="dark"] .staff-badges .badge {
            background: rgba(255, 255, 255, 0.03);
        }

        /* GLASS MODAL - matches complaint cards */
        .modal-content.glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px) saturate(150%);
            -webkit-backdrop-filter: blur(12px) saturate(150%);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: 0 12px 30px rgba(2, 6, 23, 0.08);
            padding: 0;
        }

        [data-theme="dark"] .modal-content.glass-card {
            background: rgba(17, 24, 39, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        /* ensure modal sits above everything and is interactive */
        .modal {
            z-index: 2100;
        }

        .modal .modal-dialog {
            pointer-events: auto;
        }

        /* modal dialog entrance */
        .modal .modal-dialog {
            transform: translateY(8px) scale(.995);
            transition: transform .22s cubic-bezier(.2, .9, .2, 1), opacity .22s ease;
        }

        .modal.show .modal-dialog {
            transform: translateY(0) scale(1);
        }

        .pagination-btn {
            display: inline-block;
            padding: 6px 12px;
            margin: 0 2px;
            border-radius: 6px;
            background: rgba(0, 0, 0, 0.05);
            cursor: pointer;
            transition: 0.2s;
        }

        .pagination-btn.active {
            background: #3b82f6;
            color: white;
            font-weight: bold;
        }

        .pagination-btn:hover {
            background: rgba(59, 130, 246, 0.2);
        }
    </style>
@endpush
