<button class="btn btn-primary rounded-pill w-50 m-3" data-bs-toggle="modal" data-bs-target="#upsertInventoryModal"
    onclick="resetUpsertForm()">
    <i class="bi bi-plus-circle me-2"></i> New
</button>

<div class="modal fade" id="upsertInventoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Upsert Inventory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="upsertInventoryForm">
                @csrf
                <input type="hidden" name="id" id="inventory_id">
                <div class="modal-body">
                    <div id="formAlert" class="alert d-none" role="alert"></div>

                    <div class="mb-3">
                        <label>Product</label>
                        <select class="form-control rounded-pill" name="product_id" id="inventory_product_id" required>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Supplier</label>
                        <select class="form-control rounded-pill" name="supplier_id" id="inventory_supplier_id"
                            required>
                            @foreach ($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Price</label>
                        <input type="number" class="form-control rounded-pill" name="price" id="inventory_price" required>
                    </div>

                    <div class="mb-3">
                        <label>Quantity</label>
                        <input type="number" class="form-control rounded-pill" name="quantity" id="inventory_quantity" required>
                    </div>

                    <div class="mb-3">
                        <label>Made At</label>
                        <input type="date" class="form-control rounded-pill" name="made_at" id="inventory_made_at">
                    </div>

                    <div class="mb-3">
                        <label>Expires At</label>
                        <input type="date" class="form-control rounded-pill" name="expires_at" id="inventory_expires_at">
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
        $("#upsertInventoryForm")[0].reset();
        $("#inventory_id").val("");
        $("#formAlert").addClass("d-none").text("");
    }

    $("#upsertInventoryForm").on("submit", function(e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $("#saveBtn").prop("disabled", true);
        $("#saveBtnText").text("Saving...");
        $("#saveBtnSpinner").removeClass("d-none");

        $.post("{{ oRoute('stock.inventories.upsert') }}", formData)
            .done(function(data) {
                if (data.result) {
                    let inv = data.inventory;
                    let card = inventoryCard(inv);
                    let existing = $("#inventory_" + inv.id);

                    if (existing.length) {
                        existing.replaceWith(card);
                    } else {
                        $("#inventories-container").prepend(card);
                        inventories.unshift(inv);
                    }

                    $("#formAlert").removeClass("d-none").addClass("alert-success").text("Saved successfully");
                    setTimeout(() => {
                        bootstrap.Modal.getInstance($("#upsertInventoryModal")[0]).hide();
                        resetUpsertForm();
                    }, 1200);
                }
            })
            .fail(function(xhr) {
                $("#formAlert").removeClass("d-none").addClass("alert-danger").text(xhr.responseJSON
                    ?.message || "Save failed");
            })
            .always(function() {
                $("#saveBtn").prop("disabled", false);
                $("#saveBtnText").text("Save");
                $("#saveBtnSpinner").addClass("d-none");
            });
    });

    function openEdit(id, product_id, supplier_id, price, quantity, made_at, expires_at) {
        resetUpsertForm();
        $("#inventory_id").val(id);
        $("#inventory_product_id").val(product_id);
        $("#inventory_supplier_id").val(supplier_id);
        $("#inventory_price").val(price);
        $("#inventory_quantity").val(quantity);
        $("#inventory_made_at").val(made_at);
        $("#inventory_expires_at").val(expires_at);
        new bootstrap.Modal($("#upsertInventoryModal")).show();
    }
</script>
