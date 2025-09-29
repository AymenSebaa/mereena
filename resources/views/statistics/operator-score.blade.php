@extends('layouts.app')

@section('title', 'Operator Scores')

@section('content')
    <div class="mobile-padding">
        <h3 class="mb-4">Operator Scores</h3>

        <!-- Date Filter -->
        @include('statistics.date-filter')

        <table id="operatorScoresTable" class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>Operator</th>
                    <th>Hotel</th>
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
                    @foreach ($day['operators'] as $operator)
                        <tr>
                            <td>{{ $operator['operator'] }}</td>
                            <td>{{ $operator['hotel'] ?? '-' }}</td>
                            <td>{{ $operator['zone'] ?? '-' }}</td>
                            <td><span class="badge-extra arrival">{{ $operator['total_arrivals'] ?? 0 }}</span></td>
                            <td><span class="badge-extra boarding">{{ $operator['total_boardings'] ?? 0 }}</span></td>
                            <td><span class="badge-extra departure">{{ $operator['total_departures'] ?? 0 }}</span></td>
                            <td>
                                @foreach ($operator['duplicates'] ?? [] as $type => $count)
                                    <span class="badge-extra {{ $type }}">{{ $count }}</span>
                                @endforeach
                            </td>
                            <td>{{ $day['date'] }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary details-btn" type="button"
                                    data-scans='@json($operator['scans'])'> <i class="bi bi-caret-down-fill"></i>
                                </button>
                                <a href="{{ oRoute('reports.operator-bus') }}?start={{ $start }}&end={{ $end }}&operator_id={{ $operator['operator_id'] }}"
                                    class="btn btn-sm btn-secondary" target="_blank"> <i class="bi bi-printer"></i> </a>
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
        $(document).ready(function() {
            var table = $('#operatorScoresTable').DataTable({
                pageLength: 25,
                order: [
                    [5, 'desc'],
                    [0, 'asc']
                ],
            });

            $('#operatorScoresTable tbody').on('click', 'button.details-btn', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                } else {
                    var scansGroups = $(this).data('scans'); // grouped like busScore

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
                        var time = new Date(main.created_at).toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        });

                        html += `
                            <tr class="extra-${extraType}">
                                <td class="group-text">${time}</td>
                                <td class="group-text"><span class="badge-extra ${extraType}">${extraType.charAt(0).toUpperCase() + extraType.slice(1)}</span></td>
                                <td class="group-text">${main.bus_name ?? '-'}</td>
                                <td class="group-text">${main.company?.name ?? '-'}</td>
                                <td class="group-text">${main.hotel ?? '-'}</td>
                                <td class="group-text">${main.zone ?? '-'}</td>
                                <td class="group-text">${main.distance ?? '-'}</td>
                            </tr>`;

                        // duplicates inside group
                        if (groupObj.group.length > 1) {
                            groupObj.group.slice(1).forEach(scan => {
                                var dupTime = new Date(scan.created_at).toLocaleTimeString(
                                    [], {
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit'
                                    });

                                html += `
                                    <tr class="extra-${extraType}" style="font-size:.85em;">
                                        <td><span class="duplicate-text">${dupTime}</span></td>
                                        <td><span class="duplicate-text"><span class="badge-extra ${extraType}">${extraType.charAt(0).toUpperCase() + extraType.slice(1)}</span></span></td>
                                        <td><span class="duplicate-text">${scan.bus_name ?? '-'}</span></td>
                                        <td><span class="duplicate-text">${scan.company?.name ?? '-'}</span></td>
                                        <td><span class="duplicate-text">${scan.hotel ?? '-'}</span></td>
                                        <td><span class="duplicate-text">${scan.zone ?? '-'}</span></td>
                                        <td><span class="duplicate-text">${scan.distance ?? '-'}</span></td>
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
