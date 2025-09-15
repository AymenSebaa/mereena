@extends('layouts.app')

@section('title', 'Company Score')

@section('content')
    <div class="mobile-padding">
        <h3 class="mb-4">Company Scores</h3>

        @include('statistics.date-filter')

        <table id="companyScoresTable" class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Buses used</th>
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
                    @foreach ($day['companies'] as $company)
                        <tr>
                            <td>{{ $company['company'] }}</td>
                            <td><span class="badge bg-primary">{{ $company['worked_buses'] }}</span></td>
                            <td><span class="badge-extra arrival">{{ $company['total_arrivals'] ?? 0 }}</span></td>
                            <td><span class="badge-extra boarding">{{ $company['total_boardings'] ?? 0 }}</span></td>
                            <td><span class="badge-extra departure">{{ $company['total_departures'] ?? 0 }}</span></td>
                            <td>
                                @foreach ($company['duplicates'] ?? [] as $type => $count)
                                    <span class="badge-extra {{ $type }}">{{ $count }}</span>
                                @endforeach
                            </td>
                            <td>{{ $day['date'] }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-info details-btn"
                                    data-scans='@json($company['scans'])'>View</button>
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
            var table = $('#companyScoresTable').DataTable({
                pageLength: 25,
                order: [
                    [4, 'desc'], // sort by departures
                    [0, 'asc']
                ],
            });

            $('#companyScoresTable tbody').on('click', 'button.details-btn', function() {
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
                        <th>Operator</th>
                        <th>Hotel</th>
                    </tr>
                </thead>
                <tbody>`;

                    scansGroups.forEach(groupObj => {
                        var main = groupObj.group[0];
                        var extraType = main.extra ?? 'none';
                        var time = new Date(main.created_at).toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        html += `
                <tr class="extra-${extraType}">
                    <td>${time}</td>
                    <td><span class="badge-extra ${extraType}">${extraType}</span></td>
                    <td>${main.bus_name ?? '-'}</td>
                    <td>${main.operator ?? '-'}</td>
                    <td>${main.hotel ?? '-'}</td>
                </tr>`;

                        if (groupObj.group.length > 1) {
                            groupObj.group.slice(1).forEach(scan => {
                                var dupTime = new Date(scan.created_at).toLocaleTimeString([], {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                                html += `
                        <tr style="font-size:.85em;">
                            <td><span class="duplicate-text">${dupTime}</span></td>
                            <td><span class="duplicate-text"><span class="badge-extra ${extraType}">${extraType}</span></span></td>
                            <td><span class="duplicate-text">${scan.bus_name ?? '-'}</span></td>
                            <td><span class="duplicate-text">${scan.operator ?? '-'}</span></td>
                            <td><span class="duplicate-text">${scan.hotel ?? '-'}</span></td>
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
