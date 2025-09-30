@section('extra-fields')
    <div class="mb-3">
        <label>Name</label>
        <input type="text" class="form-control rounded-pill" name="name" id="region_name" required>
    </div>

    <div class="mb-3">
        <label>M49 Code</label>
        <input type="number" class="form-control rounded-pill" name="m49_code" id="region_m49_code" required>
    </div>

    <div class="mb-3">
        <label>Continent</label>
        <select class="form-control rounded-pill" name="continent_id" id="region_continent_id" required>
            @foreach(\Modules\World\Models\Continent::all() as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
@endsection

@section('extra-fill')
    $("#region_name").val(item.name);
    $("#region_m49_code").val(item.m49_code);
    $("#region_continent_id").val(item.continent_id);
@endsection
