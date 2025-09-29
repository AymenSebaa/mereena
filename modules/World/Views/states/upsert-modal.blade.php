<div class="modal fade" id="upsertStateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Upsert State</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="upsertStateForm">
                @csrf
                <input type="hidden" name="id" id="state_id">
                <div class="modal-body">
                    <div id="formAlert" class="alert d-none"></div>
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="state_name" class="form-control rounded-pill" required>
                    </div>
                    <div class="mb-3">
                        <label>ISO2</label>
                        <input type="text" name="iso2" id="state_iso2" class="form-control rounded-pill">
                    </div>
                    <div class="mb-3">
                        <label>Latitude</label>
                        <input type="number" step="0.000001" name="lat" id="state_lat" class="form-control rounded-pill">
                    </div>
                    <div class="mb-3">
                        <label>Longitude</label>
                        <input type="number" step="0.000001" name="lng" id="state_lng" class="form-control rounded-pill">
                    </div>
                    <div class="mb-3">
                        <label>Country</label>
                        <select name="country_id" id="state_country_id" class="form-control rounded-pill" required>
                            @foreach(\Modules\World\Models\Country::all() as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark rounded-pill" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="saveBtn" class="btn btn-success rounded-pill">
                        Save <span id="saveBtnSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function resetUpsertForm() {
        $("#upsertStateForm")[0].reset();
        $("#state_id").val("");
        $("#formAlert").addClass("d-none").text("");
    }

    function openEdit(id, name, iso2, lat, lng, countryId) {
        resetUpsertForm();
        $("#state_id").val(id);
        $("#state_name").val(name);
        $("#state_iso2").val(iso2);
        $("#state_lat").val(lat);
        $("#state_lng").val(lng);
        $("#state_country_id").val(countryId);
        new bootstrap.Modal($("#upsertStateModal")).show();
    }

    $("#upsertStateForm").on("submit", function(e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $("#saveBtn").prop("disabled", true);
        $("#saveBtnSpinner").removeClass("d-none");

        $.post("{{ oRoute('world.states.upsert') }}", formData)
            .done(function(data) {
                if (data.result) {
                    let s = data.state;
                    let card = stateCard(s);
                    let existing = $("#state_" + s.id);

                    if (existing.length) {
                        existing.replaceWith(card);
                    } else {
                        $("#states-container").prepend(card);
                        states.unshift(s);
                    }

                    $("#formAlert").removeClass("d-none").addClass("alert-success").text("Saved successfully");
                    setTimeout(() => {
                        bootstrap.Modal.getInstance($("#upsertStateModal")[0]).hide();
                        resetUpsertForm();
                    }, 1200);

                    renderPagination();
                }
            })
            .fail(function(xhr) {
                $("#formAlert").removeClass("d-none").addClass("alert-danger").text(xhr.responseJSON?.message || "Save failed");
            })
            .always(function() {
                $("#saveBtn").prop("disabled", false);
                $("#saveBtnSpinner").addClass("d-none");
            });
    });
</script>