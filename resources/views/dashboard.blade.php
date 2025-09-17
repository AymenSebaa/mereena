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
                    <div class="stat-icon icon-bus">
                        <i class="bi bi-bus-front"></i>
                    </div>
                    <div class="stat-title">Départs</div>
                    <div class="stat-number">{{ $task_count ?? 0 }}</div>
                </div>

                <div class="stat-card animate-on-load">
                    <div class="stat-icon icon-alert">
                        <i class="bi bi-bell"></i>
                    </div>
                    <div class="stat-title">Alerts</div>
                    <div class="stat-number">{{ $event_count ?? 0 }}</div>
                </div>

                <div class="stat-card animate-on-load">
                    <div class="stat-icon icon-hotel">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="stat-title">Hotels</div>
                    <div class="stat-number">{{ $hotel_count ?? 0 }}</div>

                    @if ($profile->role_id == 10)
                        <small class="text-primary"> {{ $profile->hotel->name }} </small>
                    @else
                    @endif
                </div>

                <div class="stat-card animate-on-load">
                    <div class="stat-icon icon-complaint">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <div class="stat-title">Complaints</div>
                    <div class="stat-number">{{ $complaint_count ?? 0 }}</div>
                </div>

                <div class="stat-card animate-on-load">
                    <div class="stat-icon icon-bus">
                        <i class="bi bi-bus-front"></i>
                    </div>
                    <div class="stat-title">Buses</div>
                    <div class="stat-number">{{ $bus_count ?? 0 }}</div>
                </div>

                <div class="stat-card animate-on-load">
                    <div class="stat-icon icon-alert">
                        <i class="bi bi-signpost-split"></i>
                    </div>
                    <div class="stat-title">N° quais de retour</div>
                    <div class="stat-number">{{ $returnQuaisCount ?? 16 }}</div>
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

            <!-- Upcoming Departures -->
            <div class="overlay-card">
                <div class="card-title">
                    <span>Upcoming Departures</span>
                    <a href="{{ route('tasks.index') }}" class="view-all">View All</a>
                </div>

                @if (isset($recentTasks) && $recentTasks->count())
                    @foreach ($recentTasks as $task)
                        @php
                            $from = $task->pickup_time_from ? Carbon\Carbon::parse($task->pickup_time_from) : null;
                            $to = $task->pickup_time_to ? Carbon\Carbon::parse($task->pickup_time_to) : null;
                            $now = Carbon\Carbon::now();

                            $statusClass = 'status-pending';
                            $statusText = 'Pending';

                            if ($to && $to->lt($now)) {
                                $statusClass = 'status-offline';
                                $statusText = 'Offline';
                            } elseif ($from && $to && $from->lte($now) && $to->gte($now)) {
                                $statusClass = 'status-online';
                                $statusText = 'Online';
                            }

                            $routeText =
                                $task->title ??
                                ($task->hotel->name ?? 'Hotel') . ' → ' . ($task->destination ?? 'Destination');
                        @endphp

                        <div class="departure-item">
                            <div class="departure-icon">
                                <i class="fas fa-bus"></i>
                            </div>

                            <div class="departure-details">
                                <div class="departure-route">{{ $routeText }}</div>
                                <div class="departure-location">
                                    {{ $task->hotel->name ?? 'Unknown Hotel' }}
                                    @if (!empty($task->bus?->plate))
                                        &middot; {{ $task->bus->plate }}
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="departure-time">
                                        @if ($from)
                                            {{ $from->format('H:i') }}
                                        @else
                                            --:--
                                        @endif
                                        -
                                        @if ($to)
                                            {{ $to->format('H:i') }}
                                        @else
                                            --:--
                                        @endif
                                    </div>
                                    <span class="departure-status {{ $statusClass }}">{{ $statusText }}</span>
                                </div>
                            </div>

                        </div>
                    @endforeach
                @else
                    <div class="departure-item">
                        <div class="departure-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <div class="departure-details">
                            <div class="departure-route">No upcoming departures</div>
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
            <div class="overlay-card">
                <div class="card-title">
                    <span>Alerts &amp; Notifications</span>
                    <a href="{{ route('tasks.index') }}" class="view-all">View All</a>
                </div>

                <div class="departure-item">
                    <div class="departure-icon"
                        style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(229, 127, 8, 0.2)); color: var(--warning);">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="departure-details">
                        <div class="departure-route">Traffic Alert</div>
                        <div class="departure-location">Heavy traffic reported on Didouche Mourad Street</div>
                        <div class="departure-time">Updated: {{ \Carbon\Carbon::now()->format('H:i') }}</div>
                    </div>
                </div>

                <div class="departure-item">
                    <div class="departure-icon"
                        style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(79, 70, 229, 0.2)); color: var(--primary);">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="departure-details">
                        <div class="departure-route">System Maintenance</div>
                        <div class="departure-location">Scheduled this weekend from 02:00 to 04:00</div>
                        <div class="departure-time">Updated: {{ \Carbon\Carbon::now()->subHours(2)->format('H:i') }}</div>
                    </div>
                </div>

                <div class="departure-item">
                    <div class="departure-icon"
                        style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.2)); color: var(--success);">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="departure-details">
                        <div class="departure-route">New Features Available</div>
                        <div class="departure-location">Real-time tracking now available for all routes in Algiers</div>
                        <div class="departure-time">Updated: {{ \Carbon\Carbon::now()->subMinutes(30)->format('H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="overlay-card">
                <div class="card-title">
                    <span>Events & Activities</span>
                    <a href="{{ route('events.index') }}" class="view-all">View All</a>
                </div>

                <div class="departure-item">
                    <div class="departure-icon"
                        style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(79, 70, 229, 0.2)); color: var(--primary);">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="departure-details">
                        <div class="departure-route">Opening Ceremony</div>
                        <div class="departure-location">CIC Conference Center, Algiers</div>
                        <div class="departure-time">09:00 – 11:00</div>
                        <div class="departure-location small ">A celebration marking the start of IATF 2025 with
                            keynote speeches and cultural performances.</div>
                    </div>
                </div>

                <div class="departure-item">
                    <div class="departure-icon"
                        style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.2)); color: var(--success);">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="departure-details">
                        <div class="departure-route">Trade & Investment Forum</div>
                        <div class="departure-location">SAFEX Exhibition Grounds</div>
                        <div class="departure-time">11:30 – 14:00</div>
                        <div class="departure-location small ">Panel discussions with African and international
                            investors on trade facilitation and partnerships.</div>
                    </div>
                </div>

                <div class="departure-item">
                    <div class="departure-icon"
                        style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(229, 127, 8, 0.2)); color: var(--warning);">
                        <i class="fas fa-music"></i>
                    </div>
                    <div class="departure-details">
                        <div class="departure-route">Evening Gala & Networking</div>
                        <div class="departure-location">CIC Main Hall</div>
                        <div class="departure-time">19:00 – 22:00</div>
                        <div class="departure-location small ">An evening of music, cultural showcases, and
                            business networking opportunities.</div>
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
