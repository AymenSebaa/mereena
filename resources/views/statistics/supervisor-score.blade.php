@extends('layouts.app')

@section('title', 'Supervisor Scores')

@section('content')
<div class="mobile-padding">
    <h3 class="mb-4">Supervisor Scores</h3>

    @include('statistics.date-filter')

    <table id="supervisorScoresTable" class="table table-striped table-bordered w-100">
        <thead>
            <tr>
                <th>Supervisor</th>
                <th>Zone</th>
                <th>ARV</th>
                <th>BRD</th>
                <th>DPT</th>
                <th>Duplicates</th>
                <th>Date</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($result as $day)
                @foreach ($day['supervisors'] as $supervisor)
                    <tr>
                        <td>{{ $supervisor['supervisor'] }}</td>
                        <td>{{ $supervisor['zone'] ?? '-' }}</td>
                        <td><span class="badge-extra arrival">{{ $supervisor['total_arrivals'] }}</span></td>
                        <td><span class="badge-extra boarding">{{ $supervisor['total_boardings'] }}</span></td>
                        <td><span class="badge-extra departure">{{ $supervisor['total_departures'] }}</span></td>
                        <td>
                            @foreach ($supervisor['duplicates'] ?? [] as $type => $count)
                                <span class="badge-extra {{ $type }}">{{ $count }}</span>
                            @endforeach
                        </td>
                        <td>{{ $day['date'] }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary details-btn" type="button"
                                data-scans='@json($supervisor['scans'])'>
                                <i class="bi bi-caret-down-fill"></i>
                            </button>
                            <a href="{{ route('reports.supervisor-zone') }}?start={{ $start }}&end={{ $end }}&supervisor_id={{ $supervisor['supervisor_id'] }}"
                                class="btn btn-sm btn-secondary" target="_blank">
                                <i class="bi bi-printer"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    var table = $('#supervisorScoresTable').DataTable({
        pageLength: 25,
        order: [[4, 'desc'], [0, 'asc']],
    });

    $('#supervisorScoresTable tbody').on('click', 'button.details-btn', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
        } else {
            var scansGroups = $(this).data('scans');
            let html = `<table class="table table-sm table-borderless mb-0">
                <thead>
                    <tr class="table-light">
                        <th>Time</th>
                        <th>Action</th>
                        <th>Bus</th>
                        <th>Company</th>
                        <th>Hotel</th>
                        <th>Zone</th>
                        <th>Distance</th>
                    </tr>
                </thead>
                <tbody>`;

            scansGroups.forEach(groupObj => {
                var main = groupObj.group[0];
                var extraType = main.extra ?? 'none';
                var time = new Date(main.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                html += `
                <tr class="extra-${extraType}">
                    <td>${time}</td>
                    <td><span class="badge-extra ${extraType}">${extraType}</span></td>
                    <td>${main.bus_name ?? '-'}</td>
                    <td>${main.company?.name ?? '-'}</td>
                    <td>${main.hotel ?? '-'}</td>
                    <td>${main.zone ?? '-'}</td>
                    <td>${main.distance ?? '-'}</td>
                </tr>`;

                if (groupObj.group.length > 1) {
                    groupObj.group.slice(1).forEach(scan => {
                        var dupTime = new Date(scan.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        html += `
                        <tr class="extra-${extraType}" style="font-size:.85em;">
                            <td>${dupTime}</td>
                            <td><span class="badge-extra ${extraType}">${extraType}</span></td>
                            <td>${scan.bus_name ?? '-'}</td>
                            <td>${scan.company?.name ?? '-'}</td>
                            <td>${scan.hotel ?? '-'}</td>
                            <td>${scan.zone ?? '-'}</td>
                            <td>${scan.distance ?? '-'}</td>
                        </tr>`;
                    });
                }
            });

            html += `</tbody></table>`;
            row.child(html).show();
        }
    });
});
</script>
@endpush

@include('statistics.style')
