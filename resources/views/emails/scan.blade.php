<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Scan Notification</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f5f6fa; padding: 20px;">
    <div
        style="max-width: 600px; margin: auto; background-color: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">

        {{-- Banner --}}
        <div style="background-color: #fdfdfd; text-align: center; padding: 20px;">
            <img src="{{ asset('icons/iatf.jpg') }}" alt="IATF Banner" style="max-width: 100%; height: auto;">
        </div>

        {{-- Content --}}
        <div style="padding: 30px; text-align: center;">
            <h2 style="color: #333;">Hello,</h2>

            @php
                $lat = $scan->lat;
                $lng = $scan->lng;
                $mapLink = "https://www.google.com/maps?q={$lat},{$lng}";
            @endphp

            @if ($scan->type === 'hotels')
                @if ($recipientRole != 10)
                    <p>Operator <strong>{{ $scan->user->name }}</strong> has checked in Hotel
                        <strong>{{ $scan->hotel?->name ?? 'N/A' }}</strong></p>
                @else
                    <p>A check-in was recorded at Hotel <strong>{{ $scan->hotel?->name ?? 'N/A' }}</strong></p>
                @endif
                <p>Location: <a href="{{ $mapLink }}" target="_blank">{{ $lat }}, {{ $lng }}</a>
                </p>
            @elseif($scan->type === 'users')
                @if ($recipientRole != 10)
                    <p>Operator <strong>{{ $scan->user->name }}</strong> has scanned Guest
                        <strong>{{ $scan->guest?->name ?? 'N/A' }}</strong> ({{ $scan->guest?->email ?? 'N/A' }})</p>
                @else
                    <p>A guest scan was recorded: <strong>{{ $scan->guest?->name ?? 'N/A' }}</strong>
                        ({{ $scan->guest?->email ?? 'N/A' }})</p>
                @endif
                <p>Location: <a href="{{ $mapLink }}" target="_blank">{{ $lat }},
                        {{ $lng }}</a></p>
            @elseif($scan->type === 'buses')
                <p>
                    @if ($recipientRole != 10)
                        Operator <strong>{{ $scan->user->name }}</strong> declared that
                    @endif
                    Bus <strong>{{ $scan->bus?->name ?? ($scan->content['name'] ?? 'N/A') }}</strong>
                </p>
                <p>
                    @if ($scan->extra === 'departure')
                        <strong>has departed</strong> from <a href="{{ $mapLink }}"
                            target="_blank">{{ $lat }}, {{ $lng }}</a>
                    @elseif($scan->extra === 'boarding')
                        <strong>is boarding</strong> at <a href="{{ $mapLink }}"
                            target="_blank">{{ $lat }}, {{ $lng }}</a>
                    @else
                        <strong>has arrived</strong> at <a href="{{ $mapLink }}"
                            target="_blank">{{ $lat }}, {{ $lng }}</a>
                    @endif
                </p>
                <p>
                    @if ($scan->nearest_hotel_name && $scan->nearest_hotel_distance)
                        <p>Nearest hotel: <strong>{{ $scan->nearest_hotel_name }}</strong>
                            ({{ $scan->nearest_hotel_distance }})</p>
                    @endif
                </p>
            @endif

            <div style="margin-top: 30px;">
                <small style="color: #999;">© {{ date('Y') }} TFA. All rights reserved.</small>
            </div>
        </div>
    </div>
</body>

</html>
