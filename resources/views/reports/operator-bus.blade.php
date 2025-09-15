<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Rapport - Opérateur Zone</title>
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
            /* slightly reduced to avoid line breaks */
        }

        .toolbar {
            display: flex;
            justify-content: flex-end;
            gap: .5rem;
            margin-bottom: 8px;
        }

        .btn {
            display: inline-block;
            padding: .35rem .6rem;
            border-radius: .375rem;
            border: 1px solid rgba(0, 0, 0, .08);
            background: #fff;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
            border-color: rgba(13, 110, 253, .9);
        }

        header.report-head {
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 2px solid #111;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        header.report-head img.logo {
            height: 56px;
            width: auto;
            object-fit: contain;
        }

        header.report-head h1 {
            margin: 0;
            font-size: 14px;
            flex: 1;
            text-align: center;
        }

        .meta-row {
            display: flex;
            flex-direction: column;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 10px;
            font-size: 11px;
        }

        .meta-row .meta-item {
            color: var(--muted);
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin-bottom: 14px;
        }

        table.report-table th,
        table.report-table td {
            border: 1px solid var(--table-border);
            padding: 6px 8px;
            vertical-align: top;
            text-align: center;
        }

        table.report-table th {
            background: var(--header-bg);
            font-weight: 600;
            font-size: 10.5px;
        }

        .small-muted {
            color: var(--muted);
            font-size: 10px;
        }

        .sign-boxes {
            display: flex;
            justify-content: space-between;
            margin-top: 36px;
        }

        .sign {
            width: 48%;
            text-align: center;
            font-size: 11px;
        }

        .sign .line {
            margin-top: 56px;
            border-top: 1px solid #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            padding-top: 6px;
        }

        /* print helpers */
        @media print {
            .toolbar {
                display: none;
            }

            body {
                margin: 8mm;
                font-size: 10px;
            }

            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar no-print">
        <button class="btn" onclick="window.history.back()"> ← Back</button>
        <button class="btn btn-primary" id="printBtn">🖨️ Imprimer</button>
    </div>

    @if (count($operators) === 0)
        <div class="small-muted">Aucun scan trouvé pour la période demandée ({{ $start->format('Y-m-d') }} → {{ $end->format('Y-m-d') }}).</div>
    @endif

    {{-- Iterate operators; each operator will be printed on its own page --}}
    @foreach ($operators as $index => $opEntry)
        @php
            $operator = $opEntry['operator'] ?? null;
            $hotel = $opEntry['hotel'] ?? null;
            $buses = $opEntry['buses'] ?? [];
        @endphp

        <section class="report-section">
            <header class="report-head">
                <img src="{{ asset('icons/logo-round.png') }}" alt="Logo" class="logo">
                <h1>Rapport Opérateur - Zone</h1>
                <div style="width:56px;"></div>
            </header>

            <div class="meta-row d-flex">
                <div class="meta-item"><strong>Date / Heure :</strong> {{ now()->format('d/m/Y H:i') }}</div>
                <div class="meta-item" hidden ><strong>Période :</strong> {{ $start->format('d/m/Y') }} — {{ $end->format('d/m/Y') }}</div>
                <div class="meta-item"><strong>Hôtel :</strong>
                    {{ $hotel?->name ?? ($operator?->profile?->hotel?->name ?? '-') }}</div>
                <div class="meta-item"><strong>Opérateur :</strong> {{ $operator?->name ?? '—' }}</div>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>N° place</th>
                        <th>Provider</th>
                        <th>1ère entrée</th>
                        <th>Départ assuré</th>
                        <th>N° départ</th>
                        <th>Retour observé</th>
                        <th>N° retour</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($buses as $bus)
                        <tr>
                            <td>{{ $bus['matricule'] }}</td>
                            <td>{{ $bus['seats'] }}</td>
                            <td>{{ $bus['company'] ?? '-' }}</td>
                            <td>{{ $bus['first_entry'] }}</td>
                            <td style="white-space:pre-line;">{!! $bus['departures'] ? implode('<br>', $bus['departures']) : '-' !!}</td>
                            <td>{{ $bus['departures_count'] }}</td>
                            <td style="white-space:pre-line;">{!! $bus['returns'] ? implode('<br>', $bus['returns']) : '-' !!}</td>
                            <td>{{ $bus['returns_count'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="small-muted">Aucun bus enregistré pour cet opérateur sur la
                                période.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="sign-boxes">
                <div class="sign">
                    <div>VISA CHEF DE ZONE</div>
                    <div class="line">&nbsp;</div>
                </div>
                <div class="sign">
                    <div>AGENT DE POINTAGE DES BUS</div>
                    <div class="line">&nbsp;</div>
                </div>
            </div>
        </section>

        {{-- page break after every operator except last --}}
        @if ($loop->index < count($operators) - 1)
            <div class="page-break"></div>
        @endif
    @endforeach

    <script>
        document.getElementById('printBtn').addEventListener('click', function() {
            window.print();
        });

        // Auto print if ?auto=1 present (used when opening from "Print" button in operator list)
        (function() {
            try {
                var params = new URLSearchParams(window.location.search);
                if (params.get('auto') === '1') {
                    // give a small delay for rendering
                    window.setTimeout(function() {
                        window.print();
                    }, 300);
                }
            } catch (e) {
                /* ignore */ }
        })();
    </script>
</body>

</html>
