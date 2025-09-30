@section('extra-fields')
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
@endsection

@section('extra-fill')
    $("#organization_name").val(item.name);
    $("#organization_slug").val(item.slug);
    $("#organization_email").val(item.email);
    $("#organization_phone").val(item.phone);
    $("#organization_address").val(item.address);
@endsection
