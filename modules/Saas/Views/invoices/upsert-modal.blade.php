@section('extra-fields')
    <div class="mb-3">
        <label>Organization</label>
        <select class="form-control rounded-pill" name="organization_id" id="invoice_organization_id" required>
            @foreach(\Modules\Saas\Models\Organization::all() as $o)
                <option value="{{ $o->id }}">{{ $o->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Amount</label>
        <input type="number" class="form-control rounded-pill" name="amount" id="invoice_amount" required>
    </div>

    <div class="mb-3">
        <label>Status</label>
        <select class="form-control rounded-pill" name="status" id="invoice_status" required>
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
            <option value="overdue">Overdue</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Due Date</label>
        <input type="date" class="form-control rounded-pill" name="due_date" id="invoice_due_date">
    </div>
@endsection

@section('extra-fill')
    $("#invoice_organization_id").val(item.organization_id);
    $("#invoice_amount").val(item.amount);
    $("#invoice_status").val(item.status);
    $("#invoice_due_date").val(item.due_date);
@endsection
