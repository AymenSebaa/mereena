<button class="btn btn-primary rounded-pill w-50 m-3" data-bs-toggle="modal" data-bs-target="#upsertOrderModal"
    onclick="openOrderModal()">+ New Order</button>

<div class="modal fade" id="upsertOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="orderForm">
            @csrf
            <input type="hidden" id="order_id" name="order_id">

            <div class="modal-content glass-card">
                <div class="modal-header">
                    <h5 class="modal-title">Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Customer Name</label>
                        <input type="text" class="form-control rounded-pill" id="customer_name" name="customer_name">
                    </div>
                    <div class="mb-3">
                        <label>Customer Email</label>
                        <input type="email" class="form-control rounded-pill" id="customer_email" name="customer_email">
                    </div>
                    <div class="mb-3">
                        <label>Billing Address</label>
                        <textarea class="form-control rounded-pill" id="billing_address" name="billing_address"></textarea>
                    </div>

                    <hr>
                    <h6>Items</h6>
                    <div id="orderItems"></div>
                    <button type="button" class="btn btn-sm btn-success my-2 rounded-pill" onclick="addOrderItem()">+ Add Item</button>
                    <div id="stockWarning" class="alert alert-danger d-none mt-2"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill">Save Order</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let orderItemsIndex = 0;

    function openOrderModal(order = null) {
        $("#orderForm")[0].reset();
        $("#orderItems").html("");
        $("#order_id").val("");
        orderItemsIndex = 0;
        $("#stockWarning").addClass("d-none").text("");

        if (order) {
            $("#order_id").val(order.id);
            $("#customer_name").val(order.customer_name);
            $("#customer_email").val(order.customer_email);
            $("#billing_address").val(order.billing_address);

            order.items.forEach(item => {
                addOrderItem(item.product_id, item.inventory_id, item.supplier_id, item.quantity, item
                    .unit_price);
            });
        } else {
            addOrderItem(); // one empty row
        }
    }

    function addOrderItem(productId = '', inventoryId = '', supplierId = '', quantity = 1, unitPrice = 0) {
        const container = document.getElementById('orderItems');
        const index = orderItemsIndex++;

        const html = `
        <div class="dl-card mb-2 p-2" id="orderItem_${index}">
            <div class="row g-2">
                <div class="col-md-3">
                    <label>Product</label>
                    <select class="form-select rounded-pill" name="items[${index}][product_id]"
                        onchange="loadInventories(this, ${index})">
                        <option value="">-- Select --</option>
                        @foreach (\Modules\Stock\Models\Product::all() as $product)
                            <option value="{{ $product->id }}" ${productId == {{ $product->id }} ? 'selected' : ''}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Inventory</label>
                    <select class="form-select rounded-pill" name="items[${index}][inventory_id]"
                        onchange="autoSelectSupplier(this, ${index})">
                        <option value="">-- Select --</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Supplier</label>
                    <input type="text" class="form-control rounded-pill" name="items[${index}][supplier]" disabled>
                    <input type="hidden" name="items[${index}][supplier_id]">
                </div>
                <div class="col-md-2">
                    <label>Qty</label>
                    <input type="number" class="form-control rounded-pill" name="items[${index}][quantity]"
                        value="${quantity}" min="1" onchange="validateStock(this, ${index})">
                </div>
                <div class="col-md-2">
                    <label>Unit Price</label>
                    <input type="number" step="0.01" class="form-control rounded-pill"
                        name="items[${index}][unit_price]" value="${unitPrice}">
                </div>
            </div>
        </div>
    `;
        container.insertAdjacentHTML('beforeend', html);

        // If productId was passed, load inventories and preselect inventory/supplier
        if (productId) {
            setTimeout(() => {
                let productSelect = document.querySelector(
                    `#orderItem_${index} select[name="items[${index}][product_id]"]`);
                if (productSelect) {
                    productSelect.value = productId;
                    loadInventories(productSelect, index, inventoryId, supplierId);
                }
            }, 120);
        }
    }

    /**
     * Load inventories by product using route(..., ':id').replace(':id', productId)
     * This avoids concatenation issues in Blade templates.
     *
     * preselectInv / preselectSupplier are optional IDs to preselect after load.
     */
    function loadInventories(select, index, preselectInv = '', preselectSupplier = '') {
        let productId = select.value;
        let inventorySelect = document.querySelector(
        `#orderItem_${index} select[name="items[${index}][inventory_id]"]`);
        if (!productId) {
            inventorySelect.innerHTML = '<option value="">-- Select --</option>';
            autoSelectSupplier(inventorySelect, index);
            return;
        }

        inventorySelect.innerHTML = '<option value="">Loading...</option>';

        // Use route with placeholder and replace(':id', productId)
        const url = "{{ route('stock.inventories.byProduct', ':id') }}".replace(':id', encodeURIComponent(productId));

        fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) throw res;
                return res.json();
            })
            .then(data => {
                inventorySelect.innerHTML = '<option value="">-- Select --</option>';
                data.forEach(inv => {
                    const selected = inv.id == preselectInv ? 'selected' : '';
                    const supplierName = inv.supplier?.name ?? '';
                    const supplierId = inv.supplier?.id ?? '';
                    inventorySelect.innerHTML += `<option value="${inv.id}"
                    data-supplier="${supplierId}"
                    data-supplier-name="${supplierName}"
                    data-stock="${inv.quantity}"
                    ${selected}>
                    Batch ${inv.batch ?? inv.id} (Stock: ${inv.quantity})
                </option>`;
                });

                // If we have a preselected inventory, trigger supplier auto-selection
                if (preselectInv) {
                    setTimeout(() => {
                        autoSelectSupplier(inventorySelect, index);
                    }, 50);
                } else {
                    // clear supplier fields if nothing selected
                    autoSelectSupplier(inventorySelect, index);
                }
            })
            .catch(async (err) => {
                let message = 'Failed to load inventories';
                try {
                    message = (await err.json()).message || message
                } catch (e) {}
                console.error(message);
                inventorySelect.innerHTML = '<option value="">-- Error loading --</option>';
            });
    }

    function autoSelectSupplier(select, index) {
        let option = select.options[select.selectedIndex];
        let supplierInput = document.querySelector(`#orderItem_${index} input[name="items[${index}][supplier]"]`);
        let supplierIdInput = document.querySelector(`#orderItem_${index} input[name="items[${index}][supplier_id]"]`);
        supplierInput.value = option?.dataset?.supplierName || '';
        supplierIdInput.value = option?.dataset?.supplier || '';
    }

    function validateStock(input, index) {
        let qty = parseInt(input.value) || 0;
        let invSelect = document.querySelector(`#orderItem_${index} select[name="items[${index}][inventory_id]"]`);
        let stock = parseInt(invSelect.options[invSelect.selectedIndex]?.dataset.stock || 0);

        let warning = document.getElementById('stockWarning');
        if (qty > stock) {
            warning.classList.remove('d-none');
            warning.innerText = `⚠️ Quantity exceeds available stock (${stock})`;
        } else {
            // hide only if all rows are OK — simple behavior: hide when current row OK
            warning.classList.add('d-none');
        }
    }

    // submit via jQuery.ajax (unified pattern similar to inventory)
    $("#orderForm").on("submit", function(e) {
        e.preventDefault();

        let form = $(this);
        $("#saveOrderBtn")?.prop("disabled", true);

        $.ajax({
            url: "{{ route('stock.orders.upsert') }}",
            type: "POST",
            data: form.serialize(),
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            success: function(data) {
                if (data.result) {
                    // you can either reload or update DOM dynamically
                    // here we simply reload to keep things simple and safe
                    location.reload();
                }
            },
            error: function(xhr) {
                console.error("Save failed:", xhr.responseJSON?.message || xhr.statusText);
                // display inline error if you want
            },
            complete: function() {
                $("#saveOrderBtn")?.prop("disabled", false);
            }
        });
    });
</script>
