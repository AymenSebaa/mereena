@section('extra-fields')
    <div class="mb-3">
        <label>Name</label>
        <input type="text" class="form-control rounded-pill" name="name" id="country_name" required>
    </div>

    <div class="mb-3">
        <label>ISO2</label>
        <input type="text" class="form-control rounded-pill" name="iso2" id="country_iso2" maxlength="2" required>
    </div>

    <div class="mb-3">
        <label>ISO3</label>
        <input type="text" class="form-control rounded-pill" name="iso3" id="country_iso3" maxlength="3" required>
    </div>

    <div class="mb-3">
        <label>Phone Code</label>
        <input type="text" class="form-control rounded-pill" name="phone_code" id="country_phone_code">
    </div>

    <div class="mb-3">
        <label>Currency</label>
        <input type="text" class="form-control rounded-pill" name="currency" id="country_currency">
    </div>

    <div class="mb-3">
        <label>Emoji</label>
        <input type="text" class="form-control rounded-pill" name="emoji" id="country_emoji" maxlength="4">
    </div>

    <div class="mb-3">
        <label>Latitude</label>
        <input type="number" step="0.000001" class="form-control rounded-pill" name="lat" id="country_lat">
    </div>

    <div class="mb-3">
        <label>Longitude</label>
        <input type="number" step="0.000001" class="form-control rounded-pill" name="lng" id="country_lng">
    </div>

    <div class="mb-3">
        <label>Region</label>
        <select class="form-control rounded-pill" name="region_id" id="country_region_id" required>
            @foreach(\Modules\World\Models\Region::with('continent')->get() as $r)
                <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->continent->name }})</option>
            @endforeach
        </select>
    </div>
@endsection

@section('extra-fill')
    $("#country_name").val(item.name);
    $("#country_iso2").val(item.iso2);
    $("#country_iso3").val(item.iso3);
    $("#country_phone_code").val(item.phone_code);
    $("#country_currency").val(item.currency);
    $("#country_emoji").val(item.emoji);
    $("#country_lat").val(item.lat);
    $("#country_lng").val(item.lng);
    $("#country_region_id").val(item.region_id);
@endsection
