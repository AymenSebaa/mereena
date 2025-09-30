@section('extra-fields')
    <div class="mb-3">
        <label>Organization</label>
        <select class="form-control rounded-pill" name="organization_id" id="subscription_organization_id" required>
            @foreach(\Modules\Saas\Models\Organization::all() as $o)
                <option value="{{ $o->id }}">{{ $o->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Plan</label>
        <select class="form-control rounded-pill" name="plan_id" id="subscription_plan_id" required>
            @foreach(\Modules\Saas\Models\Plan::all() as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Status</label>
        <input type="text" class="form-control rounded-pill" name="status" id="subscription_status" required>
    </div>

    <div class="mb-3">
        <label>Starts At</label>
        <input type="date" class="form-control rounded-pill" name="starts_at" id="subscription_starts_at">
    </div>

    <div class="mb-3">
        <label>Ends At</label>
        <input type="date" class="form-control rounded-pill" name="ends_at" id="subscription_ends_at">
    </div>
@endsection

@section('extra-fill')
    $("#subscription_organization_id").val(item.organization_id);
    $("#subscription_plan_id").val(item.plan_id);
    $("#subscription_status").val(item.status);
    $("#subscription_starts_at").val(item.starts_at);
    $("#subscription_ends_at").val(item.ends_at);
@endsection
