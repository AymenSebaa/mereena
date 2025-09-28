<button class="btn btn-primary rounded-pill w-50 m-3" data-bs-toggle="modal" data-bs-target="#upsertCountryModal"
    onclick="resetUpsertForm()">
    <i class="bi bi-plus-circle me-2"></i> New
</button>

<div class="modal fade" id="upsertCountryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Upsert Country</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="upsertCountryForm">
                @csrf
                <input type="hidden" name="id" id="country_id">
                <div class="modal-body">
                    <div id="formAlert" class="alert d-none" role="alert"></div>

                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control rounded-pill" name="name" id="country_name" required>
                    </div>

                    <div class="mb-3">
                        <label>ISO2</label>
                        <input type="text" class="form-control rounded-pill" name="iso2" id="country_iso2" maxlength="2" required>
                    </div>

                    <div class="mb-3">
                        <label>ISO3</label>
                        <input type="text" class="form-control rounded-pill" name="iso3" id="country_iso3" maxlength="3" required>
                    </div>

                    <div class="mb-3">
                        <label>Phone Code</label>
                        <input type="text" class="form-control rounded-pill" name="phone_code" id="country_phone_code">
                    </div>

                    <div class="mb-3">
                        <label>Currency</label>
                        <input type="text" class="form-control rounded-pill" name="currency" id="country_currency">
                    </div>

                    <div class="mb-3">
                        <label>Emoji</label>
                        <input type="text" class="form-control rounded-pill" name="emoji" id="country_emoji" maxlength="4">
                    </div>

                    <div class="mb-3">
                        <label>Latitude</label>
                        <input type="number" step="0.000001" class="form-control rounded-pill" name="lat" id="country_lat">
                    </div>

                    <div class="mb-3">
                        <label>Longitude</label>
                        <input type="number" step="0.000001" class="form-control rounded-pill" name="lng" id="country_lng">
                    </div>

                    <div class="mb-3">
                        <label>Region</label>
                        <select class="form-control rounded-pill" name="region_id" id="country_region_id" required>
                            @foreach(\Modules\World\Models\Region::all() as $r)
                                <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->continent->name }})</option>
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
        $("#upsertCountryForm")[0].reset();
        $("#country_id").val("");
        $("#formAlert").addClass("d-none").text("");
    }

    $("#upsertCountryForm").on("submit", function(e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $("#saveBtn").prop("disabled", true);
        $("#saveBtnText").text("Saving...");
        $("#saveBtnSpinner").removeClass("d-none");

        $.post("{{ route('world.countries.upsert') }}", formData)
            .done(function(data) {
                if (data.result) {
                    let c = data.country;
                    let card = countryCard(c);
                    let existing = $("#country_" + c.id);

                    if (existing.length) {
                        existing.replaceWith(card);
                    } else {
                        $("#countries-container").prepend(card);
                        countries.unshift(c);
                    }

                    $("#formAlert").removeClass("d-none").addClass("alert-success").text("Saved successfully");
                    setTimeout(() => {
                        bootstrap.Modal.getInstance($("#upsertCountryModal")[0]).hide();
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

    function openEdit(id, name, iso2, iso3, phone, currency, emoji, lat, lng, regionId) {
        resetUpsertForm();
        $("#country_id").val(id);
        $("#country_name").val(name);
        $("#country_iso2").val(iso2);
        $("#country_iso3").val(iso3);
        $("#country_phone_code").val(phone);
        $("#country_currency").val(currency);
        $("#country_emoji").val(emoji);
        $("#country_lat").val(lat);
        $("#country_lng").val(lng);
        $("#country_region_id").val(regionId);
        new bootstrap.Modal($("#upsertCountryModal")).show();
    }
</script>
