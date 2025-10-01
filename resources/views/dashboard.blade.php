@extends('layouts.app')

@section('title', 'Dashboard')
@php
    $profile = auth()->user()->profile;
@endphp
@section('content')
    <div class="mobile-padding">

        <!-- Stats Cards -->
        @if (!in_array($profile->role_id, [10]))
            <div class="stats-container mb-4">
                <div class="stat-card animate-on-load">
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-title">Agents</div>
                    <div class="stat-number">{{ $agent_count ?? 0 }}</div>
                </div>

                <div class="stat-card animate-on-load">
                    <div class="stat-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="stat-title">Visits Today</div>
                    <div class="stat-number">{{ $visit_count ?? 0 }}</div>
                </div>

                <div class="stat-card animate-on-load">
                    <div class="stat-icon">
                        <i class="bi bi-capsule"></i>
                    </div>
                    <div class="stat-title">Pharmacies</div>
                    <div class="stat-number">{{ $pharmacy_count ?? 0 }}</div>
                </div>

                <div class="stat-card animate-on-load">
                    <div class="stat-icon">
                        <i class="bi bi-hospital"></i>
                    </div>
                    <div class="stat-title">Doctors</div>
                    <div class="stat-number">{{ $doctor_count ?? 0 }}</div>
                </div>

                <div class="stat-card animate-on-load">
                    <div class="stat-icon">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <div class="stat-title">Orders</div>
                    <div class="stat-number">{{ $order_count ?? 0 }}</div>
                </div>

                <div class="stat-card animate-on-load">
                    <div class="stat-icon">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
                    <div class="stat-title">Stock Alerts</div>
                    <div class="stat-number">{{ $stock_alerts ?? 0 }}</div>
                </div>
            </div>
        @endif

        <!-- Guest QR + Hotel -->
        @if (in_array($profile->role_id, [10]))
            <div class="map-overlay mb-4">
                <div class="overlay-card alerts-section animate-on-load w-100">
                    <div class="row align-items-center">
                        <div class="col-xl-4 col-lg-6 text-center mb-3 mb-md-0">
                            <div class="card glass p-3 rounded-3 d-inline-block">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($qrcode) }}&size=240x240"
                                    alt="QR Code" class="rounded-3" style="width: 240px; height: 240px;">
                            </div>
                        </div>
                        <div class="col-xl-8 col-lg-6 mt-3 d-flex flex-column justify-content-center">
                            <h3 class="h5 fw-bold mb-2">Your Guest QR Code</h3>
                            <p class="text-secondary mb-3">Scan when boarding a Bus for seamless travel experience.</p>
                            <p class="text-secondary mb-3">For more information reqeust IATF team.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($profile->hotel)
            <div class="map-overlay mb-4">
                <div class="overlay-card animate-on-load w-100">
                    <div class="card-title">
                        <span>Hotel</span>
                    </div>
                    <h4 class="d-flex align-items-center">
                        <span> {{ $profile->hotel->name }} </span>
                    </h4>
                </div>
            </div>
        @endif

        @if ($profile->zone)
            <div class="map-overlay mb-4">
                <div class="overlay-card animate-on-load w-100">
                    <div class="card-title">
                        <span>Zone</span>
                    </div>
                    <h4 class="d-flex align-items-center">
                        <span> {{ $profile->zone->name }} </span>
                    </h4>
                </div>
            </div>
        @endif

        <!-- Map Overlay Cards -->
        <div class="map-overlay mb-4">

            <div class="overlay-card">
                <div class="card-title">
                    <span>Upcoming Visits</span>
                    <a href="" class="view-all">View All</a>
                </div>

                @if (isset($upcomingVisits) && $upcomingVisits->count())
                    @foreach ($upcomingVisits as $visit)
                        @php
                            $from = $visit->time_from ? Carbon\Carbon::parse($visit->time_from) : null;
                            $to = $visit->time_to ? Carbon\Carbon::parse($visit->time_to) : null;
                        @endphp
                        <div class="departure-item">
                            <div class="departure-icon"><i class="bi bi-calendar-event"></i></div>
                            <div class="departure-details">
                                <div class="departure-route">{{ $visit->client->name }}</div>
                                <div class="departure-location">{{ $visit->client->address }}</div>
                                <div class="d-flex justify-content-between">
                                    <div class="departure-time">
                                        {{ $from?->format('H:i') ?? '--:--' }} - {{ $to?->format('H:i') ?? '--:--' }}
                                    </div>
                                    <span class="departure-status status-pending">Planned</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="departure-item">
                        <div class="departure-icon"><i class="fas fa-calendar-times"></i></div>
                        <div class="departure-details">
                            <div class="departure-route">No visits scheduled</div>
                            <div class="departure-location">You’re all caught up</div>
                            <div class="departure-time">—</div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Live Map -->
            <div class="overlay-card w-100">
                <div class="card-title">
                    <span>Live Map</span>
                    <small class="">Showing pickup locations</small>
                </div>

                <div class="alerts-section p-0 overflow-hidden">
                    <div id="dashboard-map" style="width: 100%; height: 540px; border-radius: 0 0 20px 20px;"></div>
                </div>
            </div>
        </div>

        <div class="map-overlay">
            <!-- Alerts & Notifications -->
            <div class="map-overlay">
                <div class="overlay-card">
                    <div class="card-title">
                        <span>Stock & System Alerts</span>
                        <a href="" class="view-all">View All</a>
                    </div>
                    <div class="departure-item">
                        <div class="departure-icon" style="color: var(--warning);">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="departure-details">
                            <div class="departure-route">Low Stock</div>
                            <div class="departure-location">Paracetamol 500mg below threshold</div>
                            <div class="departure-time">Updated: {{ now()->format('H:i') }}</div>
                        </div>
                    </div>
                </div>

                <div class="overlay-card">
                    <div class="card-title">
                        <span>Events & Promotions</span>
                        <a href="" class="view-all">View All</a>
                    </div>
                    <div class="departure-item">
                        <div class="departure-icon" style="color: var(--success);">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div class="departure-details">
                            <div class="departure-route">New Product Launch</div>
                            <div class="departure-location">Amoxicillin XR – National Campaign</div>
                            <div class="departure-time">Tomorrow 10:00</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script>
        let map, bounds, markers = {};

        function initDashboardMap() {
            map = new google.maps.Map(document.getElementById('dashboard-map'), {
                center: {
                    lat: 36.7601,
                    lng: 3.0503
                },
                zoom: 6,
                gestureHandling: "cooperative",
                styles: [{
                        featureType: "all",
                        elementType: "geometry",
                        stylers: [{
                            color: "#242f3e"
                        }]
                    },
                    {
                        featureType: "all",
                        elementType: "labels.text.stroke",
                        stylers: [{
                            color: "#242f3e"
                        }]
                    },
                    {
                        featureType: "all",
                        elementType: "labels.text.fill",
                        stylers: [{
                            color: "#746855"
                        }]
                    },
                    {
                        featureType: "poi",
                        elementType: "labels.text.fill",
                        stylers: [{
                            color: "#d59563"
                        }]
                    },
                    {
                        featureType: "water",
                        elementType: "geometry",
                        stylers: [{
                            color: "#17263c"
                        }]
                    }
                ]
            });

            bounds = new google.maps.LatLngBounds();
            const tasks = @json($recentTasks ?? []);

            tasks.forEach(task => {
                if (task.pickup_address_lat && task.pickup_address_lng) {
                    const position = {
                        lat: Number(task.pickup_address_lat),
                        lng: Number(task.pickup_address_lng)
                    };

                    const marker = new google.maps.Marker({
                        position,
                        map,
                        title: _(task.title || "Pickup Location"),
                        icon: {
                            url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                        }
                    });

                    const info = new google.maps.InfoWindow({
                        content: `
                        <div class="p-2 text-dark">
                            <strong>${_(task.title ?? 'Departure')}</strong><br>
                            Hotel: ${_(task.hotel?.name ?? 'N/A')}<br>
                            Pickup: ${_(task.pickup_address ?? 'N/A')}<br>
                            Time: ${_(task.pickup_time_from ?? '')} - ${_(task.pickup_time_to ?? '')}
                        </div>
                    `
                    });

                    marker.addListener("click", () => {
                        info.open(map, marker);
                    });

                    markers[task.id] = marker;
                    bounds.extend(position);
                }
            });

            if (!bounds.isEmpty()) map.fitBounds(bounds);
        }

        window.addEventListener('load', initDashboardMap);
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}" async defer></script>
@endpush
