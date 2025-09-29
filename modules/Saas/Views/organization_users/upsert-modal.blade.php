<button class="btn btn-primary rounded-pill w-50 m-3" data-bs-toggle="modal" data-bs-target="#upsertOrgUserModal"
    onclick="resetUpsertForm()">
    <i class="bi bi-plus-circle me-2"></i> New
</button>

<div class="modal fade" id="upsertOrgUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Upsert Organization User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="upsertOrgUserForm">
                @csrf
                <input type="hidden" name="id" id="org_user_id">
                <div class="modal-body">
                    <div id="formAlert" class="alert d-none" role="alert"></div>

                    <div class="mb-3">
                        <label>Organization</label>
                        <select class="form-control rounded-pill" name="organization_id" id="org_user_org_id" required>
                            @foreach(\Modules\Saas\Models\Organization::all() as $org)
                                <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>User</label>
                        <select class="form-control rounded-pill" name="user_id" id="org_user_user_id" required>
                            @foreach(\App\Models\User::all() as $usr)
                                <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Role</label>
                        <input type="text" class="form-control rounded-pill" name="role" id="org_user_role" required>
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
        $("#upsertOrgUserForm")[0].reset();
        $("#org_user_id").val("");
        $("#formAlert").addClass("d-none").text("");
    }

    $("#upsertOrgUserForm").on("submit", function(e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $("#saveBtn").prop("disabled", true);
        $("#saveBtnText").text("Saving...");
        $("#saveBtnSpinner").removeClass("d-none");

        $.post("{{ oRoute('saas.organization_users.upsert') }}", formData)
            .done(function(data) {
                if (data.result) {
                    let u = data.data;
                    let card = orgUserCard(u);
                    let existing = $("#org_user_" + u.id);

                    if (existing.length) {
                        existing.replaceWith(card);
                    } else {
                        $("#org-users-container").prepend(card);
                        orgUsers.unshift(u);
                    }

                    showToast(data.message, "success");
                    
                    setTimeout(() => {
                        bootstrap.Modal.getInstance($("#upsertOrgUserModal")[0]).hide();
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

    function openEdit(id, orgId, userId, role) {
        resetUpsertForm();
        $("#org_user_id").val(id);
        $("#org_user_org_id").val(orgId);
        $("#org_user_user_id").val(userId);
        $("#org_user_role").val(role);
        new bootstrap.Modal($("#upsertOrgUserModal")).show();
    }
</script>
