<button class="btn btn-primary rounded-pill w-50 m-3" data-bs-toggle="modal" data-bs-target="#upsertPlanModal"
    onclick="resetUpsertForm()">
    <i class="bi bi-plus-circle me-2"></i> New
</button>

<div class="modal fade" id="upsertPlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Upsert Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="upsertPlanForm">
                @csrf
                <input type="hidden" name="id" id="plan_id">
                <div class="modal-body">
                    <div id="formAlert" class="alert d-none" role="alert"></div>

                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control rounded-pill" name="name" id="plan_name" required>
                    </div>

                    <div class="mb-3">
                        <label>Slug</label>
                        <input type="text" class="form-control rounded-pill" name="slug" id="plan_slug">
                    </div>

                    <div class="mb-3">
                        <label>Price</label>
                        <input type="number" class="form-control rounded-pill" name="price" id="plan_price" required>
                    </div>

                    <div class="mb-3">
                        <label>Interval</label>
                        <select class="form-control rounded-pill" name="interval" id="plan_interval" required>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
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
        $("#upsertPlanForm")[0].reset();
        $("#plan_id").val("");
        $("#formAlert").addClass("d-none").text("");
    }

    $("#upsertPlanForm").on("submit", function(e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $("#saveBtn").prop("disabled", true);
        $("#saveBtnText").text("Saving...");
        $("#saveBtnSpinner").removeClass("d-none");

        $.post("{{ oRoute('saas.plans.upsert') }}", formData)
            .done(function(data) {
                if (data.result) {
                    let p = data.data;
                    let card = planCard(p);
                    let existing = $("#plan_" + p.id);

                    if (existing.length) {
                        existing.replaceWith(card);
                    } else {
                        $("#plans-container").prepend(card);
                        plans.unshift(p);
                    }
                    
                    showToast(data.message, "success");

                    setTimeout(() => {
                        bootstrap.Modal.getInstance($("#upsertPlanModal")[0]).hide();
                        resetUpsertForm();
                    }, 1200);
                }
            })
            .fail(function(xhr) {
                showToast(xhr.responseJSON?.message || "Save failed", "error");
            })
            .always(function() {
                $("#saveBtn").prop("disabled", false);
                $("#saveBtnText").text("Save");
                $("#saveBtnSpinner").addClass("d-none");
            });
    });

    function openEdit(id, name, slug, price, interval) {
        resetUpsertForm();
        $("#plan_id").val(id);
        $("#plan_name").val(name);
        $("#plan_slug").val(slug);
        $("#plan_price").val(price);
        $("#plan_interval").val(interval);
        new bootstrap.Modal($("#upsertPlanModal")).show();
    }
</script>
