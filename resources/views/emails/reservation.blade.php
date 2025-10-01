<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reservation Notification</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f5f6fa; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">

        {{-- Banner --}}
        <div style="background-color: #fdfdfd; text-align: center; padding: 20px;">
            <img src="{{ asset('icons/anner.png') }}" alt="IATF Banner" style="max-width: 100%; height: auto;">
        </div>

        {{-- Content --}}
        <div style="padding: 30px; text-align: left;">

            @php
                $flight = is_string($reservation->content) ? json_decode($reservation->content) : $reservation->content;
            @endphp

            @if ($recipientRole == 10)
                <h2 style="color: #333;">Hello {{ $reservation->user->name ?? 'Guest' }},</h2>

                @if ($event === 'created')
                    <p style="color: #555; font-size: 16px;">
                        Your reservation <strong>#{{ $reservation->id }}</strong> has been submitted and will be processed shortly.
                    </p>
                @else
                    <p style="color: #555; font-size: 16px;">
                        Your reservation <strong>#{{ $reservation->id }}</strong> status has been updated to 
                        <strong>{{ $reservation->status?->name ?? 'N/A' }}</strong>.
                        @if ($event === 'rejected' && $reservation->note)
                            <br><strong>Reason:</strong> {{ $reservation->note }}
                        @endif
                    </p>
                @endif
            @else
                <h2 style="color: #333;">Reservation #{{ $reservation->id }} </h2>

                <p style="color: #555; font-size: 16px;">
                    A reservation has been {{ $event === 'created' ? 'created' : $event }} by <strong>{{ $reservation->editor->name ?? 'Guest' }}</strong>.
                </p>

                <p style="color: #555; font-size: 14px;">
                    @if (in_array($recipientRole, [1, 2, 6]))
                        <i><strong>User:</strong></i> {{ $reservation->user->name ?? '-' }}<br>
                        <i><strong>Email:</strong></i> {{ $reservation->user->email ?? '-' }}<br>
                    @endif
                    <i><strong>Hotel:</strong></i> {{ $reservation->user->profile->hotel->name ?? '-' }}<br>
                    <i><strong>Pickup Time:</strong></i> {{ $reservation->pickup_time }}<br><br>

                    <i><strong>Flight:</strong></i> {{ $flight->flightNumber ?? 'N/A' }}<br>
                    @if($flight->departureOrArrival === 'departure')
                        <i><strong>Destination:</strong></i> {{ $flight->arrivalAirport->city ?? '-' }} ({{ $flight->arrivalAirport->code ?? '-' }})<br>
                    @else
                        <i><strong>Origin:</strong></i> {{ $flight->departureAirport->city ?? '-' }} ({{ $flight->departureAirport->code ?? '-' }})<br>
                    @endif
                    <i><strong>Airline:</strong></i> {{ $flight->airline->text ?? 'Unknown Airline' }}<br>
                    <i><strong>Terminal:</strong></i> {{ $flight->aircraftTerminal->text ?? '-' }}<br>
                    <i><strong>Date:</strong></i> {{ $flight->operationTime->date ?? '-' }} | 
                    <i><strong>Time:</strong></i> {{ $flight->operationTime->time ?? '-' }}<br><br>

                    <i><strong>Status:</strong></i> {{ $reservation->status->name ?? 'Pending' }} <br>
                    @if($reservation->status->name == 'Rejected' && $reservation->note)
                        <i><strong>Note / Reason:</strong></i> <span style="color:red;">{{ $reservation->note }}</span>
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
