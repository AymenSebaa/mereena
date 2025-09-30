@section('extra-fields')
    <div class="mb-3">
        <label>Name</label>
        <input type="text" class="form-control rounded-pill" name="name" id="supplier_name" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" class="form-control rounded-pill" name="email" id="supplier_email">
    </div>
    <div class="mb-3">
        <label>Phone</label>
        <input type="text" class="form-control rounded-pill" name="phone" id="supplier_phone">
    </div>
@endsection

@section('extra-fill')
    $("#supplier_name").val(item.name);
    $("#supplier_email").val(item.email);
    $("#supplier_phone").val(item.phone);
@endsection
