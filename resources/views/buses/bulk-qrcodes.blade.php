<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bulk Bus QR Codes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 16px;
        }

        .page {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(5, auto);
            gap: 8px;
            page-break-after: always;
        }

        .qrcode-card {
            text-align: center;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .qrcode-card canvas {
            width: 140px;
            height: 140px;
            margin-bottom: 8px;
        }

        @media print {
            body {
                padding: 0;
            }

            .page {
                page-break-after: always;
            }

            .qrcode-card {
                border: none;
            }
        }
    </style>
</head>

<body>

    @php $chunks = $buses->chunk(15); @endphp

    @foreach ($chunks as $pageBuses)
        <div class="page">
            @foreach ($pageBuses as $bus)
                <div class="qrcode-card" data-id="{{ $bus->id }}" data-name="{{ $bus->name }}">
                    <canvas></canvas>
                    <div><strong>{{ $bus->name }}</strong></div>
                </div>
            @endforeach
        </div>
    @endforeach

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <script src="{{ asset('js/qr-code-encryption.js') }}"></script>
    <script>
        window.onload = async function() {
            const AES_KEY = "{{ substr(config('app.key'), 0, 16) }}"; // must be 16 chars
            await generateAllQRs('.qrcode-card', 'buses', AES_KEY, 150);
            window.print();
        };
    </script>
</body>

</html>
