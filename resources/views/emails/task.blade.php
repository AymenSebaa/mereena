<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Departure Reminder</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f6fa; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        
        {{-- Banner --}}
        <div style="background-color: #fdfdfd; text-align: center; padding: 20px;">
            <img src="{{ asset('icons/anner.png') }}" alt="IATF Banner" style="max-width: 100%; height: auto;">
        </div>

        {{-- Content --}}
        <div style="padding: 30px; text-align: center;">
            <h2 style="color: #333;">Hello,</h2>

            @php
                $lat = $task->pickup_address_lat;
                $lng = $task->pickup_address_lng;
                $mapLink = "https://www.google.com/maps?q={$lat},{$lng}";
                $time = \Carbon\Carbon::parse($task->pickup_time_from)->format('d/m/Y H:i');
            @endphp

            <p>
                Bus <strong>{{ $task->bus?->name ?? 'N/A' }}</strong> will <strong>depart in 15 minutes</strong>.
            </p>

            <p>
                Pickup Address: <strong>{{ $task->pickup_address }}</strong><br>
                Scheduled Departure: <strong>{{ $time }}</strong><br>
                Location: <a href="{{ $mapLink }}" target="_blank">{{ $lat }}, {{ $lng }}</a>
            </p>

            <div style="margin-top: 30px;">
                <small style="color: #999;">© {{ date('Y') }} TFA. All rights reserved.</small>
            </div>
        </div>
    </div>
</body>
</html>
