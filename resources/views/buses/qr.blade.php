<html>
<body style="text-align:center; margin-top:50px;">
    {!! QrCode::size(200)->generate($bus->name) !!}
    <div>{{ $bus->name }}</div>
    <script>
        window.print();
    </script>
</body>
</html>
