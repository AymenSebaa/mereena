<!-- Scan Button -->
<button class="btn btn-sm btn-outline-success btn-scan">
    <i class="bi bi-upc-scan"></i>
</button>

<!-- QR Scan Modal -->
<div class="modal fade" id="qrScanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-card border-0 text-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-semibold" id="modalTitle">Scan QR Code</h5>
                <button type="button" class="btn-close" id="qrModalClose"></button>
            </div>
            <div class="modal-body">
                <!-- QR Reader -->
                <div id="qr-reader" style="width:100%; height:400px;"></div>

                <!-- Scan Info (hidden initially) -->
                <div id="scanInfo" class="d-none p-4">
                    <p id="scanName" class="fw-semibold d-flex justify-content-center"></p>
                    <div id="busOptions" class="mb-2"></div>
                </div>

            </div>
            <div class="modal-footer border-0 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-secondary" id="stopScan">Cancel</button>
                <button type="button" class="btn btn-success w-50 d-none" id="submitScan">Submit</button>
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
let html5QrCode;
let scanning = false;
let currentScanPayload = {};

// Elements
const qrModalEl = document.getElementById('qrScanModal');
const stopScanBtn = document.getElementById('stopScan');
const closeScanBtn = document.getElementById('qrModalClose');

const scanInfo = document.getElementById('scanInfo');
const scanName = document.getElementById('scanName');
const busOptions = document.getElementById('busOptions');
const submitScanBtn = document.getElementById('submitScan');
const modalTitle = document.getElementById('modalTitle');

// Stop camera
function stopScanner() {
    if (scanning && html5QrCode) {
        html5QrCode.stop().finally(() => scanning = false);
    }
}

// Reset modal to initial scan state
function resetModal() {
    stopScanner();
    scanInfo.classList.add('d-none');
    busOptions.innerHTML = '';
    submitScanBtn.classList.add('d-none');
    modalTitle.innerText = 'Scan QR Code';

    const qrReaderDiv = document.getElementById('qr-reader');
    qrReaderDiv.classList.remove('d-none');
    qrReaderDiv.innerHTML = '';
}

// Handle cancel / close
stopScanBtn.onclick = closeScanBtn.onclick = () => {
    resetModal();
    bootstrap.Modal.getInstance(qrModalEl)?.hide();
};

// Open scan modal
document.querySelector('.btn-scan').onclick = () => {
    resetModal();
    const modal = new bootstrap.Modal(qrModalEl);
    modal.show();

    qrModalEl.addEventListener('shown.bs.modal', () => {
        html5QrCode = new Html5Qrcode("qr-reader");
        scanning = true;

        html5QrCode.start(
            {facingMode: "environment"},
            {fps: 10, qrbox: 250},
            qrCodeMessage => {
                html5QrCode.pause();

                // get user location
                let lat = localStorage.getItem("user_lat");
                let lng = localStorage.getItem("user_lng");

                currentScanPayload = {
                    content: qrCodeMessage,
                    lat: lat ? parseFloat(lat) : null,
                    lng: lng ? parseFloat(lng) : null
                };

                // Preview / decrypt content
                fetch("{{ oRoute('scans.preview') }}", {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                    body: JSON.stringify({content: qrCodeMessage})
                })
                .then(r => r.json())
                .then(res => {
                    // Hide camera, show info
                    document.getElementById('qr-reader').classList.add('d-none');
                    scanInfo.classList.remove('d-none');
                    submitScanBtn.classList.remove('d-none');
                    scanName.innerText = res.name;
                    modalTitle.innerText = 'Scan info';

                    if(res.type === 'buses') {
                        busOptions.innerHTML = `
                            <label class="form-control d-flex justify-content-between">
                                Arrival
                                <input class="form-check-input" type="radio" name="busStatus" value="arrival" >
                            </label>
                            <label class="form-control d-flex justify-content-between">
                                Boarding
                                <input class="form-check-input" type="radio" name="busStatus" value="boarding" >
                            </label>
                            <label class="form-control d-flex justify-content-between" >
                                Departure
                                <input class="form-check-input" type="radio" name="busStatus" value="departure" >
                            </label>
                        `;
                    } else {
                        // For hotel/guest, just use 'scanned' as extra
                        busOptions.innerHTML = `<input type="hidden" name="busStatus" value="scanned">`;
                    }
                });
            },
            errorMessage => console.log('QR scan error', errorMessage)
        ).catch(err => console.error(err));
    }, {once: true});
};

// Submit scan
submitScanBtn.onclick = () => {
    let extra = '';
    const radios = document.querySelectorAll('input[name="busStatus"]');
    radios.forEach(r => { if(r.checked) extra = r.value; });

    fetch("{{ oRoute('scans.store') }}", {
        method:'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({...currentScanPayload, extra})
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message || 'Scan saved!');
        resetModal();
        bootstrap.Modal.getInstance(qrModalEl)?.hide();
    })
    .catch(err => {
        console.error(err);
        alert('Failed to save scan.');
    });
};
</script>
