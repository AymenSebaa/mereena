<button class="btn btn-primary rounded-pill w-50 m-3" data-bs-toggle="modal" data-bs-target="#upsertSubscriptionModal"
    onclick="resetUpsertForm()">
    <i class="bi bi-plus-circle me-2"></i> New Subscription
</button>

<div class="modal fade" id="upsertSubscriptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Upsert Subscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="upsertSubscriptionForm">
                @csrf
                <input type="hidden" name="id" id="subscription_id">
                <div class="modal-body">
                    <div id="formAlert" class="alert d-none" role="alert"></div>

                    <div class="mb-3">
                        <label>Organization</label>
                        <select class="form-control rounded-pill" name="organization_id" id="subscription_organization_id" required>
                            @foreach(\Modules\Saas\Models\Organization::all() as $o)
                                <option value="{{ $o->id }}">{{ $o->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Plan</label>
                        <select class="form-control rounded-pill" name="plan_id" id="subscription_plan_id" required>
                            @foreach(\Modules\Saas\Models\Plan::all() as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <input type="text" class="form-control rounded-pill" name="status" id="subscription_status" required>
                    </div>

                    <div class="mb-3">
                        <label>Starts At</label>
                        <input type="date" class="form-control rounded-pill" name="starts_at" id="subscription_starts_at">
                    </div>

                    <div class="mb-3">
                        <label>Ends At</label>
                        <input type="date" class="form-control rounded-pill" name="ends_at" id="subscription_ends_at">
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
        $("#upsertSubscriptionForm")[0].reset();
        $("#subscription_id").val("");
        $("#formAlert").addClass("d-none").text("");
    }

    $("#upsertSubscriptionForm").on("submit", function(e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $("#saveBtn").prop("disabled", true);
        $("#saveBtnText").text("Saving...");
        $("#saveBtnSpinner").removeClass("d-none");

        $.post("{{ oRoute('saas.subscriptions.upsert') }}", formData)
            .done(function(data) {
                if (data.result) {
                    let s = data.data;
                    let card = subscriptionCard(s);
                    let existing = $("#subscription_" + s.id);

                    if (existing.length) {
                        existing.replaceWith(card);
                    } else {
                        $("#subscriptions-container").prepend(card);
                        subscriptions.unshift(s);
                    }

                    showToast(data.message, "success");

                    setTimeout(() => {
                        bootstrap.Modal.getInstance($("#upsertSubscriptionModal")[0]).hide();
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

    function openEdit(id, orgId, planId, status, startsAt, endsAt) {
        resetUpsertForm();
        $("#subscription_id").val(id);
        $("#subscription_organization_id").val(orgId);
        $("#subscription_plan_id").val(planId);
        $("#subscription_status").val(status);
        $("#subscription_starts_at").val(startsAt);
        $("#subscription_ends_at").val(endsAt);
        new bootstrap.Modal($("#upsertSubscriptionModal")).show();
    }
</script>
