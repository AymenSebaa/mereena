<!-- License Plate Button -->
<button class="btn btn-sm btn-outline-primary btn-plate">
    <i class="bi bi-bus-front-fill"></i>
</button>

<!-- License Plate Modal -->
<div class="modal fade" id="plateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-card border-0 text-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-semibold">Bus License Plate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- License Plate Input -->
                <div class="mb-3">
                    <label class="form-label">Enter License Plate</label>
                    <input type="text" id="licensePlate" class="form-control" placeholder="12345-423-16">
                </div>

                <!-- Extra Options -->
                <label class="form-label">Status</label>
                <div id="plateOptions" class="mb-2">
                    <label class="form-control d-flex justify-content-between">
                        Arrival
                        <input class="form-check-input" type="radio" name="busStatus" value="arrival">
                    </label>
                    <label class="form-control d-flex justify-content-between">
                        Boarding
                        <input class="form-check-input" type="radio" name="busStatus" value="boarding">
                    </label>
                    <label class="form-control d-flex justify-content-between">
                        Departure
                        <input class="form-check-input" type="radio" name="busStatus" value="departure">
                    </label>
                    <label class="form-control d-flex justify-content-between">
                        None
                        <input class="form-check-input" type="radio" name="busStatus" value="none">
                    </label>
                </div>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success w-50" id="submitPlate">Submit</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelector('.btn-plate').onclick = () => {
        const modal = new bootstrap.Modal(document.getElementById('plateModal'));
        modal.show();
    };

    document.getElementById('submitPlate').onclick = () => {
        const licensePlate = document.getElementById('licensePlate').value.trim();
        if (!licensePlate) {
            alert("Please enter a license plate.");
            return;
        }

        let extra = 'none';
        const radios = document.querySelectorAll('input[name="busStatus"]');
        radios.forEach(r => {
            if (r.checked) extra = r.value;
        });

        // get user location
        let lat = localStorage.getItem("user_lat");
        let lng = localStorage.getItem("user_lng");

        const content = {
            name: licensePlate,
            type: "buses",
            type_id: null,
        }

        const payload = {
            ...content,
            extra,
            content,
            lat,
            lng
        };

        fetch("{{ route('buses.scan') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(res => {
                alert(res.message || "Bus status updated!");
                bootstrap.Modal.getInstance(document.getElementById('plateModal')).hide();
                document.getElementById('licensePlate').value = ""; // reset input
            })
            .catch(err => {
                console.error(err);
                alert("Failed to submit license plate.");
            });
    };
</script>

<style>
    .btn-plate {
        position: fixed;
        bottom: 140px;
        right: 20px;
        z-index: 1050;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: none;
        background: var(--accent, #0d6efd);
        color: #fff;
        font-size: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s ease;
    }

    .btn-plate:hover {
        background: var(--accent-dark, #0b5ed7);
    }
</style>
