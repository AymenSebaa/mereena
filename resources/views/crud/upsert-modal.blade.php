<button class="btn btn-primary rounded-pill w-50 m-3" data-bs-toggle="modal" data-bs-target="#crudUpsertModal"
    onclick="resetCrudForm()">
    <i class="bi bi-plus-circle me-2"></i> New
</button>

<div class="modal fade" id="crudUpsertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Upsert {{ $item ?? 'item' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="crudForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="crud_id">
                <div class="modal-body">
                    <div id="crudFormAlert" class="alert d-none"></div>
                    @yield('extra-fields')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark rounded-pill" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="crudSaveBtn" class="btn btn-success rounded-pill">
                        <span id="crudSaveBtnText">Save</span>
                        <span id="crudSaveBtnSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function resetCrudForm() {
        $("#crudForm")[0].reset();
        $("#crud_id").val("");
        $("#crudFormAlert").addClass("d-none").text("");

        if (window.uploaders) {
            window.uploaders.forEach(u => u.reset());
        }
    }

    $("#crudForm").on("submit", async function(e) {
        e.preventDefault();

        let formData = $(this).serialize(); // base64 hidden inputs included

        $("#crudSaveBtn").prop("disabled", true);
        $("#crudSaveBtnText").text("Saving...");
        $("#crudSaveBtnSpinner").removeClass("d-none");

        try {
            let res = await $.post("{{ oRoute($routePrefix . '.upsert') }}", formData);

            if (res.result) {
                fetchItems(); // reload list
                bootstrap.Modal.getInstance($("#crudUpsertModal")[0]).hide();
                resetCrudForm();
            }
        } catch (xhr) {
            $("#crudFormAlert")
                .removeClass("d-none alert-success")
                .addClass("alert-danger")
                .text(xhr.responseJSON?.message || "Save failed");
        } finally {
            $("#crudSaveBtn").prop("disabled", false);
            $("#crudSaveBtnText").text("Save");
            $("#crudSaveBtnSpinner").addClass("d-none");
        }
    });

    function openEdit(id) {
        resetCrudForm();
        const item = items.find(i => i.id === id);
        if (!item) {
            console.error("Item not found:", id);
            return;
        }
        $("#crud_id").val(item.id);
        @yield('extra-fill')
        new bootstrap.Modal($("#crudUpsertModal")).show();
    }
</script>
@endpush

