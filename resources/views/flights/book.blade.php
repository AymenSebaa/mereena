<x-guest-layout>
    <div class="text-center mb-4">
        <h3>✈️ Book a Flight</h3>
        <p class="text-muted">Follow the steps to book your flight transfer.</p>
    </div>

    <!-- Stepper -->
    <div class="steps mb-4 d-flex justify-content-between">
        <div class="step active" data-step="1">
            <span>1</span>
            <span>Select Flight</span>
        </div>
        <div class="step" data-step="2">
            <span>2</span>
            <span>Passenger Info</span>
        </div>
        <div class="step" data-step="3">
            <span>3</span>
            <span>Confirmation</span>
        </div>
    </div>

    <!-- Step 1 -->
    <div class="step-content" id="step-1">
        <h5 class="mb-3">Choose a Departure Flight</h5>
        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#flightModal">
            Browse Flights
        </button>

        <!-- Selected flight card -->
        <div id="selectedFlightCard" class="mt-3 d-none">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 id="flightInfo"></h5>
                    <p class="small text-muted mb-1" id="flightWarning"></p>
                    <button class="btn btn-outline-danger btn-sm mt-2" id="clearFlight">Change Flight</button>
                </div>
            </div>

            <div class="mt-3">
                <button type="button" class="btn btn-primary next-step">Continue →</button>
            </div>
        </div>
    </div>

    <!-- Step 2 -->
    <div class="step-content d-none" id="step-2">
        <h5 class="mb-3">Passenger Info</h5>

        <form id="reserveForm" method="POST" action="{{ oRoute('flights.reserve') }}">
            @csrf
            <input type="hidden" name="flight" id="selectedFlight">

            <!-- Category -->
            <div class="mb-3">
                <label for="category" class="form-label">{{ __('Category') }}</label>
                <select id="category" name="category" class="form-select rounded-pill" required>
                    <option value="">{{ __('Select your category') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category')" class="text-danger small mt-1" />
            </div>

            <!-- Extra input for "Other" -->
            <div class="mb-3 d-none" id="other-category-wrapper">
                <label for="other_category" class="form-label">{{ __('Specify Category') }}</label>
                <input id="other_category" type="text" class="form-control rounded-pill" name="other_category">
                <x-input-error :messages="$errors->get('other_category')" class="text-danger small mt-1" />
            </div>

            <!-- Country -->
            <div class="mb-3">
                <label for="country_id" class="form-label">{{ __('Country') }}</label>
                <select id="country_id" name="country_id" class="form-select rounded-pill" required>
                    <option value="">{{ __('Select your country') }}</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name_en }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('country_id')" class="text-danger small mt-1" />
            </div>

            <!-- Hotel -->
            <div class="mb-3">
                <label for="hotel_id" class="form-label">{{ __('Hotel') }}</label>
                <select id="hotel_id" name="hotel_id" class="form-select rounded-pill" required>
                    <option value="">{{ __('Select your hotel') }}</option>
                    @foreach ($hotels as $hotel)
                        <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('hotel_id')" class="text-danger small mt-1" />
            </div>

            <hr class="m-3">

            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('Name') }}</label>
                <input id="name" type="text" class="form-control rounded-pill" name="name" required>
                <x-input-error :messages="$errors->get('name')" class="text-danger small mt-1" />
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input id="email" type="email" class="form-control rounded-pill" name="email" required>
                <x-input-error :messages="$errors->get('email')" class="text-danger small mt-1" />
            </div>

            <!-- Phone -->
            <div class="mb-3">
                <label for="phone" class="form-label">{{ __('Phone') }}</label>
                <input id="phone" type="tel" class="form-control rounded-pill" name="phone" required>
                <x-input-error :messages="$errors->get('phone')" class="text-danger small mt-1" />
            </div>

            <!-- Pickup Time -->
            <div class="mb-3">
                <label for="pickup_time" class="form-label">Pickup Time (Hotel → Airport)</label>
                <input id="pickup_time" type="datetime-local" class="form-control rounded-pill" name="pickup_time"
                    required>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary prev-step">← Back</button>
                <button type="button" class="btn btn-primary next-step">Continue →</button>
            </div>
        </form>
    </div>


    <!-- Step 3 -->
    <div class="step-content d-none" id="step-3">
        <h5 class="mb-3">Confirmation</h5>
        <div id="confirmationDetails" class="p-3 border rounded bg-light"></div>
        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-outline-secondary prev-step">← Back</button>
            <button type="submit" class="btn btn-success" form="reserveForm">Confirm Reservation</button>
        </div>
    </div>

    <!-- Flight Modal -->
    <div class="modal fade" id="flightModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select a Departure Flight</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="searchFlights" class="form-control w-100"
                        placeholder="Search destination / airline">
                    <div class="row g-3 mb-2" id="flightsList">
                        @foreach ($departures as $flight)
                            @php
                                $datetime = \Carbon\Carbon::parse(
                                    $flight['operationTime']['date'] . ' ' . $flight['operationTime']['time'],
                                );
                                $hoursDiff = now()->diffInHours($datetime, false);
                            @endphp
                            <div class="col-md-12 flight-card"
                                data-destination="{{ strtolower($flight['arrivalAirport']['city'] ?? '') }}"
                                data-company="{{ strtolower($flight['airline']['text'] ?? '') }}">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <h5 class="me-2"> {{ $flight['flightNumber'] }} </h5>
                                            <span class="badge bg-{{ $flight['codeContext']['color'] }}">
                                                {{ $flight['codeContext']['text'] }} </span>
                                        </div>
                                        <button class="btn btn-sm btn-primary choose-flight"
                                            data-flight='@json($flight)'>Select</button>
                                    </div>
                                    <div class="card-body">
                                        @if ($hoursDiff < 5)
                                            <p class="text-warning small">⚠️ Pickup not guaranteed (less than 5h).</p>
                                        @endif
                                        <p class="card-text">
                                            <strong>Airline:</strong> {{ $flight['airline']['text'] }}<br>
                                            <strong>Destination:</strong> {{ $flight['arrivalAirport']['city'] }}
                                            ({{ $flight['arrivalAirport']['code'] }})
                                        </p>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between align-items-center">
                                        <span><strong>Time:</strong> {{ $flight['operationTime']['time'] }}</span>
                                        <span><strong>Date:</strong> {{ $flight['operationTime']['date'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center w-100">
        <button type="button" class="btn nav-link btn-sm mt-4" onclick="backToLogin()"> ← Back to login</button>
    </div>

    <script>
        function backToLogin() {
            window.location.href = "{{ oRoute('login') }}"
        }

        // Toggle "Other" category input
        document.addEventListener("DOMContentLoaded", function() {
            const categorySelect = document.getElementById('category');
            const otherWrapper = document.getElementById('other-category-wrapper');

            function toggleOther() {
                if (categorySelect.value === "Other") {
                    otherWrapper.classList.remove("d-none");
                } else {
                    otherWrapper.classList.add("d-none");
                }
            }

            categorySelect.addEventListener("change", toggleOther);
            toggleOther(); // run once on load
        });

        const steps = document.querySelectorAll(".step");
        const stepContents = document.querySelectorAll(".step-content");
        let currentStep = 1;
        let selectedFlight = null;

        function showStep(step) {
            steps.forEach(s => s.classList.remove("active"));
            stepContents.forEach(c => c.classList.add("d-none"));
            steps[step - 1].classList.add("active");
            document.getElementById("step-" + step).classList.remove("d-none");
            currentStep = step;
        }

        // Flight selection
        document.querySelectorAll(".choose-flight").forEach(btn => {
            btn.addEventListener("click", () => {
                selectedFlight = JSON.parse(btn.dataset.flight);
                document.getElementById("selectedFlight").value = btn.dataset.flight;

                document.getElementById("flightInfo").innerHTML =
                    `${selectedFlight.flightNumber} – ${selectedFlight.airline.text}<br>
                     Destination: ${selectedFlight.arrivalAirport.city} (${selectedFlight.arrivalAirport.code})<br>
                     Departure: ${selectedFlight.operationTime.date} ${selectedFlight.operationTime.time}`;

                const departureTime = new Date(
                    `${selectedFlight.operationTime.date}T${selectedFlight.operationTime.time}`
                );

                const hoursDiff = (departureTime - new Date()) / (1000 * 60 * 60);
                document.getElementById("flightWarning").innerText =
                    hoursDiff < 5 ? "⚠️ Pickup not guaranteed (less than 5h)." : "";

                document.getElementById("selectedFlightCard").classList.remove("d-none");
                bootstrap.Modal.getInstance(document.getElementById("flightModal")).hide();
            });
        });

        document.getElementById("clearFlight").addEventListener("click", () => {
            selectedFlight = null;
            document.getElementById("selectedFlightCard").classList.add("d-none");
        });

        // Search filter
        document.getElementById("searchFlights").addEventListener("input", (e) => {
            const query = e.target.value.toLowerCase();
            document.querySelectorAll(".flight-card").forEach(card => {
                const dest = card.dataset.destination;
                const company = card.dataset.company;
                card.style.display = (dest.includes(query) || company.includes(query)) ? "" : "none";
            });
        });

        // Step navigation
        document.querySelectorAll(".next-step").forEach(btn => {
            btn.addEventListener("click", () => {
                if (currentStep === 2) {
                    if (!selectedFlight) {
                        alert("Please select a flight before continuing.");
                        return;
                    }

                    const category = document.getElementById("category").value;
                    const otherCategory = document.getElementById("other_category").value;
                    const country = document.getElementById("country_id").value;
                    const hotel = document.getElementById("hotel_id").value;
                    const name = document.getElementById("name").value.trim();
                    const email = document.getElementById("email").value.trim();
                    const phone = document.getElementById("phone").value.trim();
                    const pickupRaw = document.getElementById("pickup_time").value;

                    // Validation checks
                    if (!category) {
                        alert("Please select a category.");
                        return;
                    }
                    if (category === "Other" && !otherCategory.trim()) {
                        alert("Please specify your category.");
                        return;
                    }
                    if (!country) {
                        alert("Please select a country.");
                        return;
                    }
                    if (!hotel) {
                        alert("Please select a hotel.");
                        return;
                    }
                    if (!name) {
                        alert("Please enter your name.");
                        return;
                    }
                    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                        alert("Please enter a valid email address.");
                        return;
                    }
                    if (!phone || phone.length < 6) {
                        alert("Please enter a valid phone number.");
                        return;
                    }
                    if (!pickupRaw) {
                        alert("Please select a pickup time.");
                        return;
                    }

                    // Build confirmation preview
                    const countrySelect = document.getElementById("country_id");
                    const hotelSelect = document.getElementById("hotel_id");
                    const pickup = pickupRaw.replace("T", " ");

                    document.getElementById("confirmationDetails").innerHTML = `
                        <h6 class="mb-2">✈️ Flight</h6>
                        <p><strong>Flight:</strong> ${selectedFlight.flightNumber} - ${selectedFlight.airline.text}</p>
                        <p><strong>Destination:</strong> ${selectedFlight.arrivalAirport.city} (${selectedFlight.arrivalAirport.code})</p>
                        <p><strong>Departure:</strong> ${selectedFlight.operationTime.date} ${selectedFlight.operationTime.time}</p>
                        <hr class='my-2'>
                        <h6 class="mb-2">👤 Passenger</h6>
                        <p><strong>Category:</strong> ${category === "Other" ? otherCategory : category}</p>
                        <p><strong>Country:</strong> ${countrySelect.options[countrySelect.selectedIndex].text}</p>
                        <p><strong>Hotel:</strong> ${hotelSelect.options[hotelSelect.selectedIndex].text}</p>
                        <p><strong>Name:</strong> ${name}</p>
                        <p><strong>Email:</strong> ${email}</p>
                        <p><strong>Phone:</strong> ${phone}</p>
                        <p><strong>Pickup Time:</strong> ${pickup}</p>
                    `;
                }

                showStep(currentStep + 1);
            });
        });

        document.querySelectorAll(".prev-step").forEach(btn => {
            btn.addEventListener("click", () => showStep(currentStep - 1));
        });
    </script>

    <style>
        #wrapper {
            min-height: 44em;
        }

        .step {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center
        }

        #flightsList {
            max-height: 33em;
            overflow: scroll;
            margin-top: .7em;
        }

        .flight-card {
            margin: 0;
            margin-bottom: .7em;
        }
    </style>
</x-guest-layout>
