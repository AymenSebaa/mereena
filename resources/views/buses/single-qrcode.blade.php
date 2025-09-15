<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bus QR Code - {{ $bus->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 50px;
            text-align: center;
        }

        canvas {
            width: 200px;
            height: 200px;
            margin-bottom: 12px;
        }
    </style>
</head>

<body>

    <canvas id="qrcode"></canvas>
    <div><strong>{{ $bus->name }}</strong></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <script src="{{ asset('js/qr-code-encryption.js') }}"></script>
    <script>
        window.onload = async function() {
            await generateEncryptedQR(
                document.getElementById('qrcode'), {
                    type: "buses",
                    type_id: "{{ $bus->id }}",
                    name: "{{ $bus->name }}"
                },
                "{{ substr(config('app.key'), 0, 16) }}",
                200
            );
            window.print();
        };
    </script>

</body>

</html>
