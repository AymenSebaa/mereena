<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $hotel->name }} QR Code</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; }
        canvas { margin-top: 20px; }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <script src="{{ asset('js/qr-code-encryption.js') }}"></script>
</head>
<body>

<h1>{{ $hotel->name }} QR Code</h1>
<canvas id="qrcode"></canvas>

<script>
    window.onload = async function() {
        await generateEncryptedQR(
            document.getElementById('qrcode'),
            { type: "hotels", type_id: "{{ $hotel->id }}", name: "{{ $hotel->name }}" },
            "{{ substr(config('app.key'), 0, 16) }}",
            250
        );
        window.print();
    };
</script>

</body>
</html>
