@section('extra-fields')
    <div class="mb-3">
        <label>Name</label>
        <input type="text" class="form-control rounded-pill" name="name" id="state_name" required>
    </div>

    <div class="mb-3">
        <label>ISO2</label>
        <input type="text" class="form-control rounded-pill" name="iso2" id="state_iso2">
    </div>

    <div class="mb-3">
        <label>Latitude</label>
        <input type="number" step="0.000001" class="form-control rounded-pill" name="lat" id="state_lat">
    </div>

    <div class="mb-3">
        <label>Longitude</label>
        <input type="number" step="0.000001" class="form-control rounded-pill" name="lng" id="state_lng">
    </div>

    <div class="mb-3">
        <label>Country</label>
        <select class="form-control rounded-pill" name="country_id" id="state_country_id" required>
            @foreach(\Modules\World\Models\Country::all() as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
@endsection

@section('extra-fill')
    $("#state_name").val(item.name);
    $("#state_iso2").val(item.iso2);
    $("#state_lat").val(item.lat);
    $("#state_lng").val(item.lng);
    $("#state_country_id").val(item.country_id);
@endsection
