<div class="modal fade" id="upsertCityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Upsert City</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="upsertCityForm">
                @csrf
                <input type="hidden" name="id" id="city_id">
                <div class="modal-body">
                    <div id="formAlert" class="alert d-none" role="alert"></div>

                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control rounded-pill" name="name" id="city_name" required>
                    </div>

                    <div class="mb-3">
                        <label>Zip Code</label>
                        <input type="text" class="form-control rounded-pill" name="zip_code" id="city_zip_code">
                    </div>

                    <div class="mb-3">
                        <label>Latitude</label>
                        <input type="number" step="0.000001" class="form-control rounded-pill" name="lat" id="city_lat">
                    </div>

                    <div class="mb-3">
                        <label>Longitude</label>
                        <input type="number" step="0.000001" class="form-control rounded-pill" name="lng" id="city_lng">
                    </div>

                    <div class="mb-3">
                        <label>State</label>
                        <select class="form-control rounded-pill" name="state_id" id="city_state_id" required>
                            @foreach(\Modules\World\Models\State::all() as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->country->name ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark rounded-pill" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="saveBtn" class="btn btn-success rounded-pill">
                        <span id="saveBtnText">Save</span>
                        <span id="saveBtnSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function resetUpsertForm() {
        $("#upsertCityForm")[0].reset();
        $("#city_id").val("");
        $("#formAlert").addClass("d-none").text("");
    }

    function openEdit(id, name, zip, lat, lng, stateId) {
        resetUpsertForm();
        $("#city_id").val(id);
        $("#city_name").val(name);
        $("#city_zip_code").val(zip);
        $("#city_lat").val(lat);
        $("#city_lng").val(lng);
        $("#city_state_id").val(stateId);
        new bootstrap.Modal($("#upsertCityModal")).show();
    }

    $("#upsertCityForm").on("submit", function(e) {
    e.preventDefault();
    let formData = $(this).serialize();

    $("#saveBtn").prop("disabled", true);
    $("#saveBtnText").text("Saving...");
    $("#saveBtnSpinner").removeClass("d-none");

    $.post("{{ route('world.cities.upsert') }}", formData)
        .done(function(data) {
            if (data.result) {
                let c = data.city;
                let card = cityCard(c);
                let existing = $("#city_" + c.id);

                if (existing.length) {
                    existing.replaceWith(card);
                } else {
                    $("#cities-container").prepend(card);
                    cities.unshift(c);
                }

                $("#formAlert").removeClass("d-none").addClass("alert-success").text("Saved successfully");
                setTimeout(() => {
                    bootstrap.Modal.getInstance($("#upsertCityModal")[0]).hide();
                    resetUpsertForm();
                }, 1200);
            }
        })
        .fail(function(xhr) {
            $("#formAlert").removeClass("d-none").addClass("alert-danger").text(xhr.responseJSON?.message || "Save failed");
        })
        .always(function() {
            $("#saveBtn").prop("disabled", false);
            $("#saveBtnText").text("Save");
            $("#saveBtnSpinner").addClass("d-none");
        });
});


</script>