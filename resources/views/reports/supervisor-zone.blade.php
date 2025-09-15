<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport - Chef de zone</title>
    <link rel="icon" href="{{ asset('icons/logo-round.png') }}" type="image/x-icon">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        :root {
            --text: #222;
            --muted: #666;
            --accent: #0d6efd;
            --table-border: #cfcfcf;
            --header-bg: #f7f7f7;
        }
        body {
            font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--text);
            margin: 18px;
            font-size: 11px;
        }
        .toolbar { display:flex; justify-content:flex-end; gap:.5rem; margin-bottom:8px; }
        .btn { padding:.35rem .6rem; border-radius:.375rem; border:1px solid rgba(0,0,0,.08); background:#fff; cursor:pointer; font-size:0.9rem; }
        .btn-primary { background:var(--accent); color:white; border-color:rgba(13,110,253,.9); }
        header.report-head { display:flex; align-items:center; gap:12px; border-bottom:2px solid #111; padding-bottom:6px; margin-bottom:10px; }
        header.report-head img.logo { height:56px; width:auto; }
        header.report-head h1 { margin:0; font-size:14px; flex:1; text-align:center; }
        .meta-row { margin:10px 0 14px 0; font-size:11px; }
        .meta-item { margin-bottom:6px; }
        table.report-table { width:100%; border-collapse:collapse; font-size:10.5px; margin-bottom:14px; }
        table.report-table th, table.report-table td { border:1px solid var(--table-border); padding:6px 8px; text-align:center; }
        table.report-table th { background:var(--header-bg); font-weight:600; font-size:10.5px; }
        .sign-boxes { display:flex; justify-content:space-between; margin-top:40px; }
        .sign { width:48%; text-align:center; font-size:11px; }
        .sign .line { margin-top:56px; border-top:1px solid #000; width:80%; margin:auto; padding-top:6px; }
        @media print { .toolbar { display:none; } body { margin:8mm; font-size:10px; } }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <button class="btn" onclick="window.history.back()"> ← Retour</button>
        <button class="btn btn-primary" id="printBtn">🖨️ Imprimer</button>
    </div>

    <section class="report-section">
        <header class="report-head">
            <img src="{{ asset('icons/logo-round.png') }}" alt="Logo" class="logo">
            <h1>ETAT DU POINTAGE JOURNALIER - Chef de zone</h1>
            <div style="width:56px;"></div>
        </header>

        <div class="meta-row">
            <div class="meta-item"><strong>Date / Heure :</strong> {{ now()->format('d/m/Y H:i') }}</div>
            <div class="meta-item"><strong>Chef de zone :</strong> {{ $supervisor->name ?? '-' }}</div>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Société</th>
                    <th>Nbre de bus</th>
                    <th>Opérationnels</th>
                    <th>Réserves</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_buses = 0;
                    $total_operational = 0;
                    $total_reserve = 0;
                @endphp
                @foreach($companies as $comp)
                    @php
                        $total_buses += $comp['bus_count'];
                        $total_operational += $comp['operational'];
                        $total_reserve += $comp['reserve'];
                    @endphp
                    <tr>
                        <td>{{ $comp['name'] }}</td>
                        <td>{{ $comp['bus_count'] }}</td>
                        <td>{{ $comp['operational'] }}</td>
                        <td>{{ $comp['reserve'] }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th>Total</th>
                    <th>{{ $total_buses }}</th>
                    <th>{{ $total_operational }}</th>
                    <th>{{ $total_reserve }}</th>
                </tr>
            </tbody>
        </table>

        <div class="sign-boxes">
            <div class="sign">
                <div>VISA COMMERCIAL FINANCE</div>
                <div class="line">&nbsp;</div>
            </div>
            <div class="sign">
                <div>VISA CHEF DE ZONE</div>
                <div class="line">&nbsp;</div>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('printBtn').addEventListener('click', function() {
            window.print();
        });
        (function(){
            var params=new URLSearchParams(window.location.search);
            if(params.get('auto')==='1'){ setTimeout(()=>window.print(),300); }
        })();
    </script>
</body>
</html>
