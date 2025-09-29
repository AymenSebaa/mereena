<button class="btn btn-primary rounded-pill w-50 m-3" data-bs-toggle="modal" data-bs-target="#upsertInvoiceModal"
    onclick="resetUpsertForm()">
    <i class="bi bi-plus-circle me-2"></i> New
</button>

<div class="modal fade" id="upsertInvoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Upsert Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="upsertInvoiceForm">
                @csrf
                <input type="hidden" name="id" id="invoice_id">
                <div class="modal-body">
                    <div id="formAlert" class="alert d-none" role="alert"></div>

                    <div class="mb-3">
                        <label>Organization</label>
                        <select class="form-control rounded-pill" name="organization_id" id="invoice_organization_id" required>
                            @foreach(\Modules\Saas\Models\Organization::all() as $o)
                                <option value="{{ $o->id }}">{{ $o->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Amount</label>
                        <input type="number" class="form-control rounded-pill" name="amount" id="invoice_amount" required>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select class="form-control rounded-pill" name="status" id="invoice_status" required>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Due Date</label>
                        <input type="date" class="form-control rounded-pill" name="due_date" id="invoice_due_date">
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
        $("#upsertInvoiceForm")[0].reset();
        $("#invoice_id").val("");
        $("#formAlert").addClass("d-none").text("");
    }

    $("#upsertInvoiceForm").on("submit", function(e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $("#saveBtn").prop("disabled", true);
        $("#saveBtnText").text("Saving...");
        $("#saveBtnSpinner").removeClass("d-none");

        $.post("{{ oRoute('saas.invoices.upsert') }}", formData)
            .done(function(data) {
                if (data.result) {
                    let i = data.data;
                    let card = invoiceCard(i);
                    let existing = $("#invoice_" + i.id);

                    if (existing.length) {
                        existing.replaceWith(card);
                    } else {
                        $("#invoices-container").prepend(card);
                        invoices.unshift(i);
                    }

                    showToast(data.message, "success");

                    setTimeout(() => {
                        bootstrap.Modal.getInstance($("#upsertInvoiceModal")[0]).hide();
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

    function openEdit(id, orgId, amount, status, dueDate) {
        resetUpsertForm();
        $("#invoice_id").val(id);
        $("#invoice_organization_id").val(orgId);
        $("#invoice_amount").val(amount);
        $("#invoice_status").val(status);
        $("#invoice_due_date").val(dueDate);
        new bootstrap.Modal($("#upsertInvoiceModal")).show();
    }
</script>
