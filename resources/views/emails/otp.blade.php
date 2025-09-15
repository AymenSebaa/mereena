<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>OTP Code</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f5f6fa; padding: 20px;">

    <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">

        {{-- Banner --}}
        <div style="background-color: #fdfdfd; text-align: center; padding: 20px;">
            <img src="{{ asset('icons/iatf.jpg') }}" alt="IATF Banner" style="max-width: 100%; height: auto;">
        </div>

        {{-- Content --}}
        <div style="padding: 30px; text-align: center;">
            <h2 style="color: #333;">Hello {{ $otp->email }},</h2>
            <p style="color: #555; font-size: 16px;">
                Thank you for registering. Use the OTP code below to verify your account:
            </p>

            {{-- OTP Code --}}
            <div style="margin: 20px 0;">
                <span style="font-size: 32px; font-weight: bold; letter-spacing: 6px; color: #{{ env('COLOR') }};">
                    {{ $otp->code }}
                </span>
            </div>

            <p style="color: #555; font-size: 14px;">
                This code will expire in <strong>10 minutes</strong>.
            </p>

            <p style="color: #555; font-size: 14px;">
                If you did not create an account, you can ignore this email.
            </p>

            <div style="margin-top: 30px;">
                <small style="color: #999;">© {{ date('Y') }} TFA. All rights reserved.</small>
            </div>
        </div>
    </div>

</body>

</html>
