<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bulk Hotel QR Codes</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .page { display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(5, auto); gap: 4px; page-break-after: always; }
        .qrcode-card { text-align: center; padding: 8px; border: 1px solid #ccc; border-radius: 8px; }
        .qrcode-card canvas { width: 140px; height: 140px; margin-bottom: 8px; }
        @media print { body { padding: 0; } .page { page-break-after: always; } .qrcode-card { border: none; } }
    </style>
</head>
<body>

@php $chunks = $hotels->chunk(15); @endphp

@foreach ($chunks as $pageHotels)
    <div class="page">
        @foreach ($pageHotels as $hotel)
            <div class="qrcode-card" data-id="{{ $hotel->id }}" data-name="{{ $hotel->name }}">
                <canvas></canvas>
                <div><strong>{{ $hotel->name }}</strong></div>
            </div>
        @endforeach
    </div>
@endforeach

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script src="{{ asset('js/qr-code-encryption.js') }}"></script>
<script>
       window.onload = async function() {
        const AES_KEY = "{{ substr(config('app.key'), 0, 16) }}"; // must be 16 chars
        await generateAllQRs('.qrcode-card', 'hotels', AES_KEY, 150);
        window.print();
    };
</script>

</body>
</html>
