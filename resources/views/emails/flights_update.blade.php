<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Flights update</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f5f6fa; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">

        {{-- Banner --}}
        <div style="background-color: #fdfdfd; text-align: center; padding: 20px;">
            <img src="{{ asset('icons/anner.png') }}" alt="IATF Banner" style="max-width: 100%; height: auto;">
        </div>

        {{-- Content --}}
        <div style="padding: 30px; text-align: center;">
            <h3>Dear Valued TFA User,</h3>

            <p style="text-align: start; margin-top: 2em;" >
                We're thrilled to announce that the Transport For Algiers (TFA) app has just gotten even better
                with our latest update! Now, you can stay connected to your travel plans like never before
                with these exciting new features:
            </p>

            <p style="text-align: start;  margin-top: 2em;" >
                <img width="27" height="27" src="{{ asset('emails/icons/ic_1.jpg') }}" align="left" hspace="12">
                <strong>Real-Time Flight Tracking:</strong> <br>
                Search and view live departure and arrival data for flights at
                Algiers Houari Boumediene Airport (ALG). Stay updated with accurate, up-to-the-minute information
                to plan your journey with confidence.
            </p>

            <p style="text-align:center;" >
                <img width="163" height="341" src="{{ asset('emails/screenshots/sc_1.png') }}">
                <img width="163" height="341" src="{{ asset('emails/screenshots/sc_2.png') }}">
            </p>

            <p style="text-align:start; margin-top: 2em;" >
                <img width="27" height="27" src="{{ asset('emails/icons/ic_1.jpg') }}" align="left"
                    hspace="12">
                <strong>Flight Status Updates:</strong> <br>
                Check the status of any flight in real time—whether it's on time, delayed,
                canceled, or more. Never miss a beat with instant updates at your fingertips.
            </p>

            <p style="text-align:center; margin-top: 2em;">
                <img width="163" height="341" src="{{ asset('emails/screenshots/sc_3.png') }}">
            </p>

            <p>
                Whether you're catching a flight, picking up a loved one, or just staying informed,
                the updated TFA app is your go-to companion for seamless travel planning.
            </p>

            <p>Best regards, <br> The <strong>Transport For Algiers</strong> Team</p>
        </div>
    </div>
</body>

</html>
