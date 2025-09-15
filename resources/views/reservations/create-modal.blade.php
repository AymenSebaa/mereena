<!-- New Reservation Button -->
<button class="btn btn-primary ms-3 text-nowrap" data-bs-toggle="modal" data-bs-target="#reservationModal">
    <i class="bi bi-plus-circle me-1"></i> New
</button>

<!-- Reservation Modal (Authenticated flow: Step 1 = Flight, Step 2 = Pickup, Step 3 = Confirm) -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">✈️ New Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Stepper -->
                <div class="steps mb-4 d-flex justify-content-between">
                    <div class="step active" data-step="1">
                        <span>1</span>
                        <span>Select Flight</span>
                    </div>
                    <div class="step" data-step="2">
                        <span>2</span>
                        <span>Pickup Time</span>
                    </div>
                    <div class="step" data-step="3">
                        <span>3</span>
                        <span>Confirmation</span>
                    </div>
                </div>

                <!-- Step 1 -->
                <div class="step-content" id="step-1">
                    <h5 class="mb-3">Select Flight</h5>

                    <!-- Selected Flight Card -->
                    <div id="selectedFlightCard" class="card mb-3 d-none">
                        <div class="card-body">
                            <div id="flightInfo"></div>
                            <div id="flightWarning" class="text-warning small mt-2"></div>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="clearFlight">Clear
                                Flight</button>
                        </div>
                    </div>

                    <!-- Step 1 Actions -->
                    <div class="d-flex justify-content-between">
                        <!-- Flight chooser (default) -->
                        <button type="button" id="chooseFlightBtn" class="btn btn-outline-primary"
                            data-bs-toggle="modal" data-bs-target="#flightModal">
                            Choose Flight
                        </button>

                        <!-- Next button (hidden until flight is chosen) -->
                        <button type="button" id="nextFromFlight" class="btn btn-primary d-none next-step">
                            Continue →
                        </button>
                    </div>
                </div>



                <!-- Step 2 (Pickup Time) -->
                <div class="step-content d-none" id="step-2">
                    <h5 class="mb-3">Pickup Time</h5>
                    <div class="mb-3">
                        <label for="pickup_time" class="form-label">Pickup Time (Hotel → Airport)</label>
                        <input id="pickup_time" type="datetime-local" class="form-control" name="pickup_time" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary prev-step">← Back</button>
                        <button type="button" class="btn btn-primary next-step">Continue →</button>
                    </div>
                </div>

                <!-- Step 3 (Confirmation) -->
                <div class="step-content d-none" id="step-3">
                    <h5 class="mb-3">Confirmation</h5>
                    <form id="reserveForm" method="POST" action="{{ route('reservations.reserve') }}">
                        @csrf
                        <input type="hidden" name="flight" id="selectedFlight">
                        <input type="hidden" name="pickup_time" id="hiddenPickupTime">

                        <div id="confirmationDetails" class="p-3 border rounded bg-light"></div>
                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-outline-secondary prev-step">← Back</button>
                            <button type="submit" class="btn btn-success">Confirm Reservation</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Flight Modal -->
<div class="modal fade" id="flightModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select a Departure Flight</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchFlights" class="form-control w-100 mb-3"
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
                                            {{ $flight['codeContext']['text'] }}
                                        </span>
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

<script>
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

    // Reset modal state when opened
    document.getElementById("reservationModal").addEventListener("hidden.bs.modal", () => {
        selectedFlight = null;
        document.getElementById("selectedFlight").value = "";
        document.getElementById("pickup_time").value = "";
        document.getElementById("hiddenPickupTime").value = "";
        document.getElementById("selectedFlightCard").classList.add("d-none");
        document.getElementById("confirmationDetails").innerHTML = "";
        showStep(1);
    });

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

            // Swap buttons
            document.getElementById("chooseFlightBtn").classList.add("d-none");
            document.getElementById("nextFromFlight").classList.remove("d-none");

            // Close flight modal & reopen reservation modal
            bootstrap.Modal.getInstance(document.getElementById("flightModal")).hide();
            const reservationModal = new bootstrap.Modal(document.getElementById("reservationModal"));
            reservationModal.show();
        });
    });

    // Clear flight resets buttons
    document.getElementById("clearFlight").addEventListener("click", () => {
        selectedFlight = null;
        document.getElementById("selectedFlightCard").classList.add("d-none");

        document.getElementById("chooseFlightBtn").classList.remove("d-none");
        document.getElementById("nextFromFlight").classList.add("d-none");
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
            if (currentStep === 1) {
                if (!selectedFlight) {
                    alert("Please select a flight before continuing.");
                    return;
                }
            }

            if (currentStep === 2) {
                const pickupInput = document.getElementById("pickup_time").value;
                if (!pickupInput) {
                    alert("Pickup time is required.");
                    return;
                }
                document.getElementById("hiddenPickupTime").value = pickupInput;

                // Build confirmation details
                document.getElementById("confirmationDetails").innerHTML = `
                    <h6 class="mb-2">✈️ Flight</h6>
                    <p><strong>Flight:</strong> ${selectedFlight.flightNumber} - ${selectedFlight.airline.text}</p>
                    <p><strong>Destination:</strong> ${selectedFlight.arrivalAirport.city} (${selectedFlight.arrivalAirport.code})</p>
                    <p><strong>Departure:</strong> ${selectedFlight.operationTime.date} ${selectedFlight.operationTime.time}</p>
                    <p><strong>Pickup Time:</strong> ${pickupInput}</p>
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
    .step {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center
    }

    #flightsList {
        max-height: 33em;
        overflow-y: auto;
        margin-top: .7em;
    }

    .flight-card {
        margin-bottom: .7em;
    }

    /* ---------------------------
   Modal + Stepper (Light by default)
--------------------------- */
    .modal-content {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 1rem;
    }

    .modal-header,
    .modal-footer {
        border-color: rgba(0, 0, 0, 0.08);
    }

    .step {
        flex: 1;
        text-align: center;
        padding: 0.5rem;
        font-size: 0.9rem;
        color: #444;
        transition: color 0.2s, font-weight 0.2s;
    }

    .step span:first-child {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        background: #e9ecef;
        margin-bottom: 0.25rem;
        font-weight: 600;
    }

    .step.active span:first-child {
        background: var(--bs-primary);
        color: #fff;
    }

    .step.active {
        font-weight: 600;
        color: var(--bs-primary);
    }

    /* Confirmation card (light) */
    #confirmationDetails {
        background: #f8f9fa;
        color: #212529;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    /* ---------------------------
   Dark Theme Overrides
--------------------------- */
    [data-theme="dark"] .modal-content {
        background: rgba(23, 31, 45, 0.92);
        border: 1px solid rgba(255, 255, 255, 0.06);
        color: #e0e6f0;
    }

    [data-theme="dark"] .modal-header,
    [data-theme="dark"] .modal-footer {
        border-color: rgba(255, 255, 255, 0.08);
    }

    [data-theme="dark"] .step {
        color: #aaa;
    }

    [data-theme="dark"] .step span:first-child {
        background: rgba(255, 255, 255, 0.08);
        color: #ccc;
    }

    [data-theme="dark"] .step.active {
        color: var(--bs-primary);
    }

    [data-theme="dark"] .step.active span:first-child {
        background: var(--bs-primary);
        color: #fff;
    }

    /* Flight list cards inside flight modal */
    [data-theme="dark"] .flight-card .card {
        background: rgba(32, 41, 56, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.05);
        color: #e0e6f0;
    }

    [data-theme="dark"] .flight-card .card-header,
    [data-theme="dark"] .flight-card .card-footer {
        border-color: rgba(255, 255, 255, 0.08);
    }

    /* Confirmation block */
    [data-theme="dark"] #confirmationDetails {
        background: rgba(40, 50, 65, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #e0e6f0;
    }

    /* Inputs in step 2 */
    [data-theme="dark"] input.form-control {
        background: rgba(40, 50, 65, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #e0e6f0;
    }

    [data-theme="dark"] input.form-control::placeholder {
        color: #888;
    }

    /* Dark mode support for custom elements */
    [data-theme="dark"] #selectedFlightCard {
        background-color: #1e1e1e !important;
        border: 1px solid #444;
        color: #e9ecef;
    }

    [data-theme="dark"] #selectedFlightCard .card-body {
        background-color: #1e1e1e;
        color: #e9ecef;
    }

    [data-theme="dark"] #confirmationDetails {
        background-color: #2b2b2b !important;
        border: 1px solid #444;
        color: #f1f1f1;
    }

    /* Optional: tweak text-warning to be readable on dark */
    [data-theme="dark"] #selectedFlightCard .text-warning {
        color: #ffc107 !important;
    }

    .modal {
        background: #333A !important;
        backdrop-filter: blur(4px) !important;
    }
</style>
