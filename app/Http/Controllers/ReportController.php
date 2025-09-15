<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Scan;

class ReportController extends Controller {

    public function operatorBus(Request $request) {
        $start = $request->start
            ? Carbon::parse($request->start)->startOfDay()
            : now()->startOfDay();

        $end = $request->end
            ? Carbon::parse($request->end)->endOfDay()
            : now()->endOfDay();

        $operatorId = $request->query('operator_id');

        $query = Scan::with(['bus.company', 'user.profile.hotel'])
            ->where('type', 'buses')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at');

        if ($operatorId) {
            // Filter scans by operator id
            $query->where('user_id', $operatorId);
        }

        $scans = $query->get();

        // Group scans by operator (or just one if operatorId given)
        $operators = $scans
            ->groupBy(fn($s) => $s->user?->id ?? 'unknown')
            ->map(function ($opScans) {
                $operatorModel = $opScans->first()->user ?? null;
                $hotelModel = $operatorModel?->profile?->hotel ?? null;

                // For each bus handled by this operator, build the row data
                $buses = $opScans
                    ->groupBy(fn($s) => $s->bus?->id ?? ($s->content['name'] ?? 'unknown'))
                    ->map(function ($busScans) {
                        $firstRow = $busScans->first();
                        $busModel = $firstRow->bus ?? null;

                        $name = $busModel?->name ?? ($firstRow->content['name'] ?? '-');
                        $company = $busModel?->company?->name ?? '-';
                        $provider = $busModel?->provider?->name ?? '-'; // if exists

                        // 1st entry: first "arrival", otherwise earliest scan
                        $firstArrivalScan = $busScans->where('extra', 'arrival')->sortBy('created_at')->first();
                        if (!$firstArrivalScan) {
                            $firstArrivalScan = $busScans->sortBy('created_at')->first();
                        }
                        $firstEntry = $firstArrivalScan?->created_at?->format('H:i') ?? '-';

                        // Departures deduped by 15 minutes
                        $departureTimes = [];
                        $lastDepTime = null;
                        foreach ($busScans->where('extra', 'departure')->sortBy('created_at') as $depScan) {
                            if ($lastDepTime === null || $depScan->created_at->diffInMinutes($lastDepTime) >= 15) {
                                $departureTimes[] = $depScan->created_at->format('H:i');
                                $lastDepTime = $depScan->created_at;
                            }
                        }

                        // Returns: arrival after a departure
                        $returns = [];
                        $seenDeparture = false;
                        foreach ($busScans->sortBy('created_at') as $scan) {
                            if ($scan->extra === 'departure') {
                                $seenDeparture = true;
                                continue;
                            }
                            if ($scan->extra === 'arrival' && $seenDeparture) {
                                $returns[] = $scan->created_at->format('H:i');
                                $seenDeparture = false;
                            }
                        }

                        return [
                            'matricule' => $name,
                            'seats' => '',
                            'provider' => $provider,
                            'company' => $company,
                            'first_entry' => $firstEntry,
                            'departures' => $departureTimes,
                            'departures_count' => count($departureTimes),
                            'returns' => $returns,
                            'returns_count' => count($returns),
                        ];
                    })
                    ->values()
                    ->toArray();

                return [
                    'operator' => $operatorModel,
                    'hotel' => $hotelModel,
                    'buses' => $buses,
                ];
            })
            ->values();

        // If operatorId is present, only return that one
        if ($operatorId) {
            $operators = $operators->take(1);
        }

        return view('reports.operator-bus', [
            'operators' => $operators,
            'start' => $start,
            'end' => $end,
        ]);
    }

    public function supervisorZone(Request $request) {
        $start = $request->start
            ? Carbon::parse($request->start)->startOfDay()
            : now()->startOfDay();

        $end = $request->end
            ? Carbon::parse($request->end)->endOfDay()
            : now()->endOfDay();

        $supervisorId = $request->query('supervisor_id');

        // Fetch supervisor with zone_id
        $supervisor = \App\Models\User::with('profile')
            ->where('id', $supervisorId)
            ->first();

        if (!$supervisor || !$supervisor->profile?->zone_id) {
            return back()->withErrors(['supervisor' => 'Invalid supervisor or missing zone']);
        }

        $zoneId = $supervisor->profile->zone_id;

        // Get operators belonging to this zone
        $operatorIds = \App\Models\User::whereHas('profile.hotel.zones', function ($q) use ($zoneId) {
            $q->where('zones.id', $zoneId);
        })->pluck('id');

        // Get scans for those operators
        $scans = Scan::with(['bus.company', 'user.profile.hotel'])
            ->where('type', 'buses')
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('user_id', $operatorIds)
            ->orderBy('created_at')
            ->get();

        // Group by operator
        $operators = $scans
            ->groupBy(fn($s) => $s->user?->id ?? 'unknown')
            ->map(function ($opScans) {
                $operatorModel = $opScans->first()->user ?? null;
                $hotelModel = $operatorModel?->profile?->hotel ?? null;

                $buses = $opScans
                    ->groupBy(fn($s) => $s->bus?->id ?? ($s->content['name'] ?? 'unknown'))
                    ->map(function ($busScans) {
                        $firstRow = $busScans->first();
                        $busModel = $firstRow->bus ?? null;

                        $name = $busModel?->name ?? ($firstRow->content['name'] ?? '-');
                        $company = $busModel?->company?->name ?? '-';
                        $provider = $busModel?->provider?->name ?? '-';

                        return [
                            'matricule' => $name,
                            'company'   => $company,
                            'provider'  => $provider,
                        ];
                    })
                    ->values()
                    ->toArray();

                return [
                    'operator' => $operatorModel,
                    'hotel'    => $hotelModel,
                    'buses'    => $buses,
                ];
            })
            ->values();

        // Aggregate companies (instead of providers)
        $companies = [];
        foreach ($operators as $op) {
            foreach ($op['buses'] as $bus) {
                $companyName = $bus['company'] ?: 'Inconnu';
                if (!isset($companies[$companyName])) {
                    $companies[$companyName] = [
                        'name'        => $companyName,
                        'bus_count'   => 0,
                        'operational' => 0, // you can adjust based on your own logic
                        'reserve'     => 0, // idem
                    ];
                }
                $companies[$companyName]['bus_count']++;
                $companies[$companyName]['operational']++; // default: count all as operational
            }
        }

        return view('reports.supervisor-zone', [
            'supervisor' => $supervisor,
            'companies'  => array_values($companies),
            'start'      => $start,
            'end'        => $end,
        ]);
    }
}
