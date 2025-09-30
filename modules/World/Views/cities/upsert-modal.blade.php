@section('extra-fields')
    <div class="mb-3">
        <label>Name</label>
        <input type="text" class="form-control rounded-pill" name="name" id="city_name" required>
    </div>

    <div class="mb-3">
        <label>Zip Code</label>
        <input type="text" class="form-control rounded-pill" name="zip_code" id="city_zip_code">
    </div>

    <div class="mb-3">
        <label>Latitude</label>
        <input type="number" step="0.000001" class="form-control rounded-pill" name="lat" id="city_lat">
    </div>

    <div class="mb-3">
        <label>Longitude</label>
        <input type="number" step="0.000001" class="form-control rounded-pill" name="lng" id="city_lng">
    </div>

    <div class="mb-3">
        <label>State</label>
        <select class="form-control rounded-pill" name="state_id" id="city_state_id" required>
            @foreach(\Modules\World\Models\State::all() as $s)
                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->country->name ?? 'N/A' }})</option>
            @endforeach
        </select>
    </div>
@endsection

@section('extra-fill')
    $("#city_name").val(item.name);
    $("#city_zip_code").val(item.zip_code);
    $("#city_lat").val(item.lat);
    $("#city_lng").val(item.lng);
    $("#city_state_id").val(item.state_id);
@endsection
