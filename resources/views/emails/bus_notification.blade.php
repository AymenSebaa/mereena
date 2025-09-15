<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bus {{ $type }} Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f6fa; padding: 20px;">

    <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">

        {{-- Banner --}}
        <div style="background-color: #0d6efd; text-align: center; padding: 20px;">
            <img src="{{ asset('icons/iatf.jpg') }}" alt="IATF Banner" style="max-width: 100%; height: auto;">
        </div>

        {{-- Content --}}
        <div style="padding: 30px; text-align: center;">
            <h2 style="color: #333;">Hello {{ $user->name }},</h2>
            <p style="color: #555; font-size: 16px;">
                This is a reminder for your upcoming <strong>{{ $type }}</strong> trip.
            </p>

            {{-- Trip Info --}}
            <div style="margin: 20px 0; text-align: left; background:#f8f9fa; padding:20px; border-radius:8px;">
                <p style="margin:0; font-size:15px; color:#333;">
                    🚌 <strong>Bus:</strong> {{ $task->bus->name ?? 'Bus' }}
                </p>
                <p style="margin:0; font-size:15px; color:#333;">
                    🏨 <strong>Hotel:</strong> {{ $task->hotel->name ?? 'Hotel' }}
                </p>
                <p style="margin:10px 0 0; font-size:15px; color:#333;">
                    📅 <strong>Date:</strong> {{ \Carbon\Carbon::parse($type == 'Departure' ? $task->pickup_time_from : $task->delivery_time_from)->format('Y-m-d') }}
                </p>
                <p style="margin:0; font-size:15px; color:#333;">
                    ⏰ <strong>Time:</strong> 
                    {{ \Carbon\Carbon::parse($type == 'Departure' ? $task->pickup_time_from : $task->delivery_time_from)->format('H:i') }}
                </p>
            </div>

            <p style="color: #555; font-size: 14px;">
                Please be ready on time. The bus will not wait beyond the scheduled departure.
            </p>

            <p style="color: #555; font-size: 14px;">
                Safe travels!
            </p>

            <div style="margin-top: 30px;">
                <small style="color: #999;">© {{ date('Y') }} TFA. All rights reserved.</small>
            </div>
        </div>
    </div>

</body>
</html>
