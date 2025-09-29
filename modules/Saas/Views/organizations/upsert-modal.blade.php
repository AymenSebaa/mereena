<button class="btn btn-primary rounded-pill w-50 m-3" data-bs-toggle="modal" data-bs-target="#upsertOrganizationModal"
    onclick="resetUpsertForm()">
    <i class="bi bi-plus-circle me-2"></i> New
</button>

<div class="modal fade" id="upsertOrganizationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Upsert Organization</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="upsertOrganizationForm">
                @csrf
                <input type="hidden" name="id" id="organization_id">
                <div class="modal-body">
                    <div id="formAlert" class="alert d-none" role="alert"></div>

                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control rounded-pill" name="name" id="organization_name" required>
                    </div>

                    <div class="mb-3">
                        <label>Slug</label>
                        <input type="text" class="form-control rounded-pill" name="slug" id="organization_slug">
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control rounded-pill" name="email" id="organization_email">
                    </div>

                    <div class="mb-3">
                        <label>Phone</label>
                        <input type="text" class="form-control rounded-pill" name="phone" id="organization_phone">
                    </div>

                    <div class="mb-3">
                        <label>Address</label>
                        <input type="text" class="form-control rounded-pill" name="address" id="organization_address">
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
        $("#upsertOrganizationForm")[0].reset();
        $("#organization_id").val("");
        $("#formAlert").addClass("d-none").text("");
    }

    $("#upsertOrganizationForm").on("submit", function(e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $("#saveBtn").prop("disabled", true);
        $("#saveBtnText").text("Saving...");
        $("#saveBtnSpinner").removeClass("d-none");

        $.post("{{ route('saas.organizations.upsert') }}", formData)
            .done(function(data) {
                if (data.result) {
                    let o = data.data;
                    let card = organizationCard(o);
                    let existing = $("#organization_" + o.id);

                    if (existing.length) {
                        existing.replaceWith(card);
                    } else {
                        $("#organizations-container").prepend(card);
                        organizations.unshift(o);
                    }

                    showToast(data.message, "success");

                    setTimeout(() => {
                        bootstrap.Modal.getInstance($("#upsertOrganizationModal")[0]).hide();
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


    function openEdit(id, name, slug, email, phone, address) {
        resetUpsertForm();
        $("#organization_id").val(id);
        $("#organization_name").val(name);
        $("#organization_slug").val(slug);
        $("#organization_email").val(email);
        $("#organization_phone").val(phone);
        $("#organization_address").val(address);
        new bootstrap.Modal($("#upsertOrganizationModal")).show();
    }
</script>
