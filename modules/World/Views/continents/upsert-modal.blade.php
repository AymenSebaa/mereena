<button class="btn btn-primary rounded-pill w-50 m-3" data-bs-toggle="modal" data-bs-target="#upsertRegionModal"
    onclick="resetUpsertForm()">
    <i class="bi bi-plus-circle me-2"></i> New
</button>

<div class="modal fade" id="upsertRegionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Upsert Region</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="upsertRegionForm">
                @csrf
                <input type="hidden" name="id" id="region_id">
                <div class="modal-body">
                    <div id="formAlert" class="alert d-none" role="alert"></div>

                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control rounded-pill" name="name" id="region_name" required>
                    </div>

                    <div class="mb-3">
                        <label>M49 Code</label>
                        <input type="number" class="form-control rounded-pill" name="m49_code" id="region_m49_code" required>
                    </div>

                    <div class="mb-3">
                        <label>Continent</label>
                        <select class="form-control rounded-pill" name="continent_id" id="region_continent_id" required>
                            @foreach(\Modules\World\Models\Continent::all() as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
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
        $("#upsertRegionForm")[0].reset();
        $("#region_id").val("");
        $("#formAlert").addClass("d-none").text("");
    }

    $("#upsertRegionForm").on("submit", function(e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $("#saveBtn").prop("disabled", true);
        $("#saveBtnText").text("Saving...");
        $("#saveBtnSpinner").removeClass("d-none");

        $.post("{{ route('world.regions.upsert') }}", formData)
            .done(function(data) {
                if (data.result) {
                    let r = data.region;
                    let card = regionCard(r);
                    let existing = $("#region_" + r.id);

                    if (existing.length) {
                        existing.replaceWith(card);
                    } else {
                        $("#regions-container").prepend(card);
                        regions.unshift(r);
                    }

                    $("#formAlert").removeClass("d-none").addClass("alert-success").text("Saved successfully");
                    setTimeout(() => {
                        bootstrap.Modal.getInstance($("#upsertRegionModal")[0]).hide();
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

    function openEdit(id, name, m49, continentId) {
        resetUpsertForm();
        $("#region_id").val(id);
        $("#region_name").val(name);
        $("#region_m49_code").val(m49);
        $("#region_continent_id").val(continentId);
        new bootstrap.Modal($("#upsertRegionModal")).show();
    }
</script>
