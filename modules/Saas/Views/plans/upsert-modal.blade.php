@section('extra-fields')
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
@endsection

@section('extra-fill')
    $("#plan_name").val(item.name);
    $("#plan_slug").val(item.slug);
    $("#plan_price").val(item.price);
    $("#plan_interval").val(item.interval);
@endsection
