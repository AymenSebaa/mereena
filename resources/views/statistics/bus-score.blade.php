@extends('layouts.app')

@section('title', 'Bus Scores')

@section('content')
    <div class="mobile-padding">
        <h3 class="mb-4">Bus Scores</h3>

        <!-- Date Filter -->
        @include('statistics.date-filter')

        <table id="busScoresTable" class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>Bus</th>
                    <th>Company</th>
                    <th>ARV</th>
                    <th>BRD</th>
                    <th>DPT</th>
                    <th>Returned</th>
                    <th>Duplicates</th>
                    <th>Date</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($result as $day)
                    @foreach ($day['buses'] as $bus)
                        <tr>
                            <td>{{ $bus['bus_name'] ?? '-' }}</td>
                            <td>{{ $bus['company']['name'] ?? '-' }}</td>
                            <td><span class="badge-extra arrival">{{ $bus['total_arrivals'] ?? 0 }}</span></td>
                            <td><span class="badge-extra boarding">{{ $bus['total_boardings'] ?? 0 }}</span></td>
                            <td><span class="badge-extra departure">{{ $bus['total_departures'] ?? 0 }}</span></td>
                            <td>
                                @if (!empty($bus['returned']))
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-warning">No</span>
                                @endif
                            </td>
                            <td>
                                @foreach ($bus['duplicates'] ?? [] as $type => $count)
                                    <span class="badge-extra {{ $type }}">{{ $count }}</span>
                                @endforeach
                            </td>
                            <td>{{ $day['date'] }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info details-btn" type="button"
                                    data-scans='@json($bus['scans'])'> View </button>
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
            var table = $('#busScoresTable').DataTable({
                pageLength: 25,
                order: [
                    [5, 'desc'],
                    [0, 'desc']
                ],
            });

            $('#busScoresTable tbody').on('click', 'button.details-btn', function() {
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
                        <th>Operator</th>
                        <th>Distance</th>
                        <th>Hotel</th>
                        <th>Zone</th>
                    </tr>
                </thead>
                <tbody>`;

                    scansGroups.forEach(groupObj => {
                        var main = groupObj.group[0];
                        var extraType = main.extra ?? 'none';
                        var operator = main.operator ?? '-';
                        var hotel = main.hotel ?? '-';
                        var zone = main.zone ?? '-';
                        var distance = main.distance ?? '-';
                        var time = new Date(main.created_at).toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        });

                        html += `
                            <tr class="extra-${extraType}">
                                <td class='group-text'>${time}</td>
                                <td class='group-text'><span class="badge-extra ${extraType}">${extraType.charAt(0).toUpperCase() + extraType.slice(1)}</span></td>
                                <td class='group-text'>${operator}</td>
                                <td class='group-text'>${distance}</td>
                                <td class='group-text'>${hotel}</td>
                                <td class='group-text'>${zone}</td>
                            </tr>`;

                        if (groupObj.group.length > 1) {
                            groupObj.group.slice(1).forEach(scan => {
                                var time = new Date(scan.created_at).toLocaleTimeString(
                                    [], {
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit'
                                    });
                                var distance = scan.distance ?? '-';
                                var hotel = scan.hotel ?? '-';
                                html += `
                                    <tr class="extra-${extraType} ps-4" style="font-size:.85em;">
                                        <td><span class='duplicate-text'>${time}</span></td>
                                        <td><span class='duplicate-text'><span class="badge-extra ${extraType}">${extraType.charAt(0).toUpperCase() + extraType.slice(1)}</span></span></td>
                                        <td><span class='duplicate-text'>${operator}</span></td>
                                        <td><span class='duplicate-text'>${distance}</span></td>
                                        <td><span class='duplicate-text'>${hotel}</span></td>
                                        <td><span class='duplicate-text'>${zone}</span></td>
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
