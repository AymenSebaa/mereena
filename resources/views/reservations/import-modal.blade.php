<!-- Import Reservations -->
<button class="btn btn-success ms-3 text-nowrap" data-bs-toggle="modal" data-bs-target="#importModal">
    <i class="bi bi-file-earmark-excel me-1"></i> Import
</button>

<!-- File input (hidden, triggered by modal) -->
<input type="file" id="importFileInput" accept=".xlsx,.xls" class="d-none">

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Reservations Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="file" id="importFile" class="form-control mb-3" accept=".xlsx,.xls">
                <div class="table-responsive">
                    <table class="table table-bordered" id="importPreviewTable">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Name</th>
                                <th>Return Date</th>
                                <th>Flight</th>
                                <th>Pickup Time</th>
                                <th>Departure</th>
                                <th>User Exists</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmImportBtn">Confirm Import</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
document.getElementById('importFile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const sheetName = workbook.SheetNames[0];
        const sheet = workbook.Sheets[sheetName];
        const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

        const tbody = document.querySelector('#importPreviewTable tbody');
        tbody.innerHTML = '';

        // skip header row
        jsonData.slice(1).forEach(async row => {
            const [category, name, returnDate, flight, pickup, departure] = row;

            // Check if user exists by name via AJAX
            const exists = await checkUserExists(name) ? '(exists)' : '(new)';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${category}</td>
                <td>${name} ${exists}</td>
                <td>${returnDate}</td>
                <td>${flight}</td>
                <td>${pickup}</td>
                <td>${departure}</td>
                <td>${exists}</td>
            `;
            tbody.appendChild(tr);
        });
    };
    reader.readAsArrayBuffer(file);
});

// Simulated AJAX check user by name
async function checkUserExists(name) {
    try {
        const res = await fetch(`{{route('reservations.users')}}?name=${encodeURIComponent(name)}`);
        const data = await res.json();
        return data.exists;
    } catch(e) {
        console.error(e);
        return false;
    }
}

// Confirm import (send to server)
document.getElementById('confirmImportBtn').addEventListener('click', async () => {
    const rows = Array.from(document.querySelectorAll('#importPreviewTable tbody tr')).map(tr => {
        const tds = tr.querySelectorAll('td');
        return {
            category: tds[0].innerText,
            name: tds[1].innerText.replace(' (exists)', '').replace(' (new)', ''),
            returnDate: tds[2].innerText,
            flight: tds[3].innerText,
            pickup: tds[4].innerText,
            departure: tds[5].innerText,
        };
    });

    // Send to server
    const res = await fetch('{{ oRoute("reservations.import") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ reservations: rows })
    });

    const data = await res.json();
    if (data.success) {
        alert('Reservations imported!');
        location.reload();
    }
});
</script>
