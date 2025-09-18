<div class="d-flex justify-content-between align-items-center">
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="startDate" class="form-label">Start Date</label>
            <input type="date" name="start" id="startDate" class="form-control"
                value="{{ request('start', $start->format('Y-m-d')) }}">
        </div>
        <div class="col-md-4">
            <label for="endDate" class="form-label">End Date</label>
            <input type="date" name="end" id="endDate" class="form-control"
                value="{{ request('end', $end->format('Y-m-d')) }}">
        </div>
        <div class="col-md-4 align-self-end">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    <div class="">

    </div>
</div>
