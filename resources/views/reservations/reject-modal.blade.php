  @if (in_array('reservations.reject', $permissions))
      <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
              <div class="modal-content glass-card">
                  <div class="modal-header">
                      <h5 class="modal-title">Reject Reservation</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                      <div class="mb-3">
                          <label for="rejectReason" class="form-label">Reason for rejection</label>
                          <textarea id="rejectReason" class="form-control" rows="3"></textarea>
                      </div>
                      <input type="hidden" id="rejectReservationId">
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="button" class="btn btn-danger" id="confirmRejectBtn">Reject</button>
                  </div>
              </div>
          </div>
      </div>
  @endif

  <script>
      // Confirm reject function
      @if (in_array('reservations.reject', $permissions))
          document.getElementById('confirmRejectBtn').addEventListener('click', async () => {
              const id = document.getElementById('rejectReservationId').value;
              const note = document.getElementById('rejectReason').value;

              try {
                  const res = await fetch(`{{ route('reservations.reject', ':id') }}`.replace(':id', id), {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/json',
                          'X-CSRF-TOKEN': '{{ csrf_token() }}'
                      },
                      body: JSON.stringify({
                          note
                      })
                  });
                  const data = await res.json();
                  if (data.success) {
                      fetchReservations();
                      bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
                  }
              } catch (err) {
                  console.error(err);
              }
          });
      @endif
  </script>
