<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Complaint Notification</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f5f6fa; padding: 20px;">
    <div
        style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">

        {{-- Banner --}}
        <div style="background-color: #fdfdfd; text-align: center; padding: 20px;">
            <img src="{{ asset('icons/iatf.jpg') }}" alt="IATF Banner" style="max-width: 100%; height: auto;">
        </div>

        {{-- Content --}}
        <div style="padding: 30px; text-align: center;">
            <h2 style="color: #333;">
                Hello {{ $complaint->user->name }},
            </h2>

            {{-- Guest --}}
            @if ($recipientRole == 10 && $event === 'created')
                <p style="color: #555; font-size: 16px;">
                    Your complaint <strong>"{{ $complaint->subject }}"</strong> has been submitted and will be reviewed
                    shortly.
                </p>
            @elseif ($recipientRole == 10 && $event === 'status_updated')
                <p style="color: #555; font-size: 16px;">
                    Your complaint <strong>"{{ $complaint->subject }}"</strong> status has been updated to
                    <strong>{{ $complaint->status?->name ?? 'N/A' }}</strong>.
                </p>
            @else
                {{-- Staff --}}
                <p style="color: #555; font-size: 16px;">
                    A complaint has been {{ $event === 'created' ? 'created' : 'updated' }} by
                    <strong>{{ $complaint->user->name }}</strong>.
                </p>
                <p style="color: #555; font-size: 14px;">
                    <strong>Type:</strong> {{ $complaint->type?->name ?? 'N/A' }}<br>
                    <strong>Subject:</strong> {{ $complaint->subject }}<br>
                    <strong>Status:</strong> {{ $complaint->status?->name ?? 'Open' }}
                </p>
            @endif

            <div style="margin-top: 30px;">
                <small style="color: #999;">© {{ date('Y') }} TFA. All rights reserved.</small>
            </div>
        </div>
    </div>
</body>

</html>
