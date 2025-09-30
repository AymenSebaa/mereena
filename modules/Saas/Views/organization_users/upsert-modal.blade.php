@section('extra-fields')
    <div class="mb-3">
        <label>Organization</label>
        <select class="form-control rounded-pill" name="organization_id" id="org_user_org_id" required>
            @foreach(\Modules\Saas\Models\Organization::all() as $org)
                <option value="{{ $org->id }}">{{ $org->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>User</label>
        <select class="form-control rounded-pill" name="user_id" id="org_user_user_id" required>
            @foreach(\App\Models\User::all() as $usr)
                <option value="{{ $usr->id }}">{{ $usr->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Role</label>
        <input type="text" class="form-control rounded-pill" name="role" id="org_user_role" required>
    </div>
@endsection

@section('extra-fill')
    $("#org_user_org_id").val(item.organization_id);
    $("#org_user_user_id").val(item.user_id);
    $("#org_user_role").val(item.role);
@endsection
