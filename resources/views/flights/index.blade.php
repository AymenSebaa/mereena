@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Flights (Terminal Ouest)</h1>

        {{-- Search --}}
        <input type="text" id="flightSearch" class="form-control mb-4" placeholder="Search flights, airline, city...">

        {{-- Tabs --}}
        <ul class="nav nav-tabs" id="flightTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button"
                    role="tab">All</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="arrivals-tab" data-bs-toggle="tab" data-bs-target="#arrivals" type="button"
                    role="tab">Arrivals</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="departures-tab" data-bs-toggle="tab" data-bs-target="#departures"
                    type="button" role="tab">Departures</button>
            </li>
        </ul>

        <div class="tab-content mt-3">
            {{-- All --}}
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                <div class="row g-3">
                    @foreach ($flights as $flight)
                        <div class="col-md-4 flight-card">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        {{ $flight['flightNumber'] }}
                                        <span class="badge bg-{{ $flight['codeContext']['color'] }}">
                                            {{ $flight['codeContext']['text'] }}
                                        </span>
                                    </h5>
                                    <p class="card-text">
                                        <strong>Airline:</strong> {{ $flight['airline']['text'] }}<br>
                                        @if ($flight['departureOrArrival'] === 'departure')
                                            <strong>Destination:</strong> {{ $flight['arrivalAirport']['city'] }}
                                            ({{ $flight['arrivalAirport']['code'] }})
                                            <br>
                                        @else
                                            <strong>Origin:</strong> {{ $flight['departureAirport']['city'] }}
                                            ({{ $flight['departureAirport']['code'] }})<br>
                                        @endif
                                        <strong>Date:</strong> {{ $flight['operationTime']['date'] }}<br>
                                        <strong>Time:</strong> {{ $flight['operationTime']['time'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Arrivals --}}
            <div class="tab-pane fade" id="arrivals" role="tabpanel">
                <div class="row g-3">
                    @foreach ($arrivals as $flight)
                        <div class="col-md-4 flight-card">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        {{ $flight['flightNumber'] }}
                                        <span class="badge bg-{{ $flight['codeContext']['color'] }}">
                                            {{ $flight['codeContext']['text'] }}
                                        </span>
                                    </h5>
                                    <p class="card-text">
                                        <strong>Airline:</strong> {{ $flight['airline']['text'] }}<br>
                                        <strong>Origin:</strong> {{ $flight['departureAirport']['city'] }}
                                        ({{ $flight['departureAirport']['code'] }})
                                        <br>
                                        <strong>Date:</strong> {{ $flight['operationTime']['date'] }}<br>
                                        <strong>Time:</strong> {{ $flight['operationTime']['time'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Departures --}}
            <div class="tab-pane fade" id="departures" role="tabpanel">
                <div class="row g-3">
                    @foreach ($departures as $flight)
                        <div class="col-md-4 flight-card">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        {{ $flight['flightNumber'] }}
                                        <span class="badge bg-{{ $flight['codeContext']['color'] }}">
                                            {{ $flight['codeContext']['text'] }}
                                        </span>
                                    </h5>
                                    <p class="card-text">
                                        <strong>Airline:</strong> {{ $flight['airline']['text'] }}<br>
                                        <strong>Destination:</strong> {{ $flight['arrivalAirport']['city'] }}
                                        ({{ $flight['arrivalAirport']['code'] }})
                                        <br>
                                        <strong>Date:</strong> {{ $flight['operationTime']['date'] }}<br>
                                        <strong>Time:</strong> {{ $flight['operationTime']['time'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- JS Search --}}
    <script>
        document.getElementById('flightSearch').addEventListener('keyup', function() {
            let query = this.value.toLowerCase();
            document.querySelectorAll('.flight-card').forEach(card => {
                let text = card.innerText.toLowerCase();
                card.style.display = text.includes(query) ? '' : 'none';
            });
        });
    </script>

    {{-- Flights Dark Mode --}}
    <style>
        /* Flight cards */
        .flight-card .card {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        [data-theme="dark"] .flight-card .card {
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e5e7eb;
        }

        [data-theme="dark"] .flight-card .card .card-title {
            color: #f9fafb;
        }

        .flight-card .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        /* Tabs light mode */
        .nav-tabs .nav-link {
            color: #111827;
            /* gray-900 */
        }

        /* Tabs dark mode */
        [data-theme="dark"] .nav-tabs .nav-link {
            color: #9ca3af;
            /* gray-400 for inactive */
        }

        [data-theme="dark"] .nav-tabs .nav-link.active {
            color: #f9fafb;
            /* white for active */
            background-color: transparent;
            border-color: #4b5563 #4b5563 #1e293b;
            /* subtle gray borders */
        }
    </style>
@endsection
