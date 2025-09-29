<!-- scan.blade.php -->

<!-- Floating Scan Button -->
<button class="btn btn-sm btn-outline-success btn-scan">
    <i class="bi bi-upc-scan"></i>
</button>

<!-- Unified QR Scan Modal -->
<div class="modal fade" id="scanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-card border-0 text-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-semibold" id="scanModalTitle">Scan QR Code</h5>
                <button type="button" class="btn-close btn-close-scan"></button>
            </div>
            <div class="modal-body">
                <!-- QR Reader -->
                <div id="scan-reader" style="width:100%; height:400px;"></div>

                <!-- Info -->
                <div id="scanInfo" class="d-none p-4">
                    <p id="scanName" class="fw-semibold d-flex justify-content-center"></p>
                    <div id="busOptions" class="mb-2"></div>
                    <div id="departureFormWrapper" class="d-none"></div>
                </div>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-secondary btn-stop-scan">Cancel</button>
                <button type="button" class="btn btn-success w-50 d-none btn-submit-scan">Submit</button>
            </div>
        </div>
    </div>
</div>

<style>
.btn-scan {
    position: fixed;
    bottom: 80px;
    right: 20px;
    z-index: 1050;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: none;
    background: var(--primary, #0d6efd);
    color: #fff;
    font-size: 1.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s ease;
}
.btn-scan:hover { background: var(--primary-dark, #0b5ed7); }

#busOptions .form-check { cursor: pointer; margin-bottom: 5px; }
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const modalEl = document.getElementById('scanModal');
    const scanBtn = document.querySelector('.btn-scan');

    let qrCodeScanner;
    let scanning = false;
    let currentPayload = {};
    let isDepartureFlow = false;
    let cachedBusData = null;

    const els = {
        closeBtn: modalEl.querySelector('.btn-close-scan'),
        stopBtn: modalEl.querySelector('.btn-stop-scan'),
        scanInfo: modalEl.querySelector('#scanInfo'),
        scanName: modalEl.querySelector('#scanName'),
        busOptions: modalEl.querySelector('#busOptions'),
        formWrapper: modalEl.querySelector('#departureFormWrapper'),
        submitBtn: modalEl.querySelector('.btn-submit-scan'),
        reader: modalEl.querySelector('#scan-reader'),
        title: modalEl.querySelector('#scanModalTitle')
    };

    function stopScanner() {
        if (scanning && qrCodeScanner) {
            qrCodeScanner.stop().finally(() => scanning = false);
        }
    }

    function resetModal() {
        stopScanner();
        els.scanInfo.classList.add('d-none');
        els.busOptions.innerHTML = '';
        els.formWrapper.innerHTML = '';
        els.formWrapper.classList.add('d-none');
        els.submitBtn.classList.add('d-none');
        els.title.innerText = 'Scan QR Code';
        isDepartureFlow = false;
        cachedBusData = null;

        els.reader.classList.remove('d-none');
        els.reader.innerHTML = '';
    }

    els.stopBtn.onclick = els.closeBtn.onclick = () => {
        resetModal();
        bootstrap.Modal.getInstance(modalEl)?.hide();
    };

    scanBtn.onclick = () => {
        resetModal();
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        modalEl.addEventListener('shown.bs.modal', () => {
            qrCodeScanner = new Html5Qrcode("scan-reader");
            scanning = true;

            qrCodeScanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                qrCodeMessage => {
                    qrCodeScanner.pause();

                    let lat = localStorage.getItem("user_lat");
                    let lng = localStorage.getItem("user_lng");

                    currentPayload = {
                        content: qrCodeMessage,
                        lat: lat ? parseFloat(lat) : null,
                        lng: lng ? parseFloat(lng) : null
                    };

                    fetch("{{ oRoute('scans.preview') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json','X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ content: qrCodeMessage })
                    })
                    .then(r => r.json())
                    .then(res => {
                        els.reader.classList.add('d-none');
                        els.scanInfo.classList.remove('d-none');
                        els.submitBtn.classList.remove('d-none');
                        els.scanName.innerText = res.name;
                        els.title.innerText = 'Scan info';

                        if (res.type === 'buses') {
                            els.busOptions.innerHTML = `
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
                                <label class="form-control d-flex justify-content-between mt-4">
                                    New Departure
                                    <input class="form-check-input" type="radio" name="busStatus" value="newDeparture">
                                </label>
                            `;
                        } else {
                            els.busOptions.innerHTML = `<input type="hidden" name="busStatus" value="scanned">`;
                        }
                    });
                },
                err => console.log('QR scan error', err)
            ).catch(err => console.error(err));
        }, { once: true });
    };

    els.submitBtn.onclick = () => {
        let extra = '';
        els.busOptions.querySelectorAll('input[name="busStatus"]').forEach(r => {
            if (r.checked) extra = r.value;
        });

        // === New Departure flow ===
        if (extra === 'newDeparture' && !isDepartureFlow) {
            isDepartureFlow = true;

            fetch("{{ oRoute('buses.decryptQr') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ content: currentPayload.content })
            })
            .then(r => r.json())
            .then(res => {
                if (res.status) {
                    cachedBusData = res;

                    els.formWrapper.classList.remove('d-none');
                    els.formWrapper.innerHTML = `
                        <form id="departureForm" class="p-3">
                            <div class="mb-2">
                                <label>License Plate</label>
                                <input class="form-control" value="${res.bus.name}" disabled>
                            </div>
                            <div class="mb-2">
                                <label>Source</label>
                                <input class="form-control" value="${res.source.name}" disabled>
                                <input type="hidden" name="pickup_address" value="${res.source.name}">
                                <input type="hidden" name="pickup_address_lat" value="${res.source.lat ?? 0}">
                                <input type="hidden" name="pickup_address_lng" value="${res.source.lng ?? 0}">
                            </div>
                            <div class="mb-2">
                                <label>Destination</label>
                                <select class="form-control" name="destination_id">
                                    ${res.destinations.map(d => `<option value="${d.id}" data-lat="${d.lat ?? 0}" data-lng="${d.lng ?? 0}">${d.name}</option>`).join('')}
                                </select>
                            </div>
                            <div class="mb-2">
                                <label>Title</label>
                                <input id="departureTitle" class="form-control" value="${res.source.name} -> (select destination)" disabled>
                            </div>
                        </form>
                    `;

                    els.formWrapper.querySelector('select[name="destination_id"]').onchange = (e) => {
                        const destName = e.target.options[e.target.selectedIndex].text;
                        document.getElementById('departureTitle').value = res.source.name + " -> " + destName;
                    };
                }
            });
            return;
        }

        // === Save Departure Task ===
        if (isDepartureFlow && cachedBusData) {
            const form = document.getElementById('departureForm');
            const destinationSelect = form.querySelector('select[name="destination_id"]');
            const destName = destinationSelect.options[destinationSelect.selectedIndex].text;
            const destLat = destinationSelect.options[destinationSelect.selectedIndex].dataset.lat;
            const destLng = destinationSelect.options[destinationSelect.selectedIndex].dataset.lng;

            fetch("{{ oRoute('tasks.upsert') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    title: cachedBusData.source.name + " -> " + destName,
                    pickup_address: cachedBusData.source.name,
                    pickup_address_lat: cachedBusData.source.lat,
                    pickup_address_lng: cachedBusData.source.lng,
                    delivery_address: destName,
                    delivery_address_lat: destLat,
                    delivery_address_lng: destLng,
                    device_id: cachedBusData.bus.id,
                    hotel_id: cachedBusData.source.id
                })
            })
            .then(r => r.json())
            .then(taskRes => {
                alert(taskRes.message || 'Departure created!');
                resetModal();
                bootstrap.Modal.getInstance(modalEl)?.hide();
            });
            return;
        }

        // === Default scan save ===
        fetch("{{ oRoute('scans.store') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ ...currentPayload, extra })
        })
        .then(r => r.json())
        .then(res => {
            alert(res.message || 'Scan saved!');
            resetModal();
            bootstrap.Modal.getInstance(modalEl)?.hide();
        })
        .catch(err => {
            console.error(err);
            alert('Failed to save scan.');
        });
    };
});
</script>
