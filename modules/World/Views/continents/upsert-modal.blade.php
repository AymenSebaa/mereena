@section('extra-fields')
    <div class="mb-3">
        <label>Name</label>
        <input type="text" class="form-control rounded-pill" name="name" id="continent_name" required>
    </div>
@endsection

@section('extra-fill')
    $("#continent_name").val(item.name);
@endsection
