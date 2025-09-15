<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Scan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatisticController extends Controller {
    public function busScore(Request $request) {
        $start = $request->start
            ? Carbon::parse($request->start)->startOfDay()
            : now()->subWeek()->startOfDay();
        $end = $request->end
            ? Carbon::parse($request->end)->endOfDay()
            : now()->endOfDay();

        $scans = Scan::with(['user.profile.hotel.zones', 'bus.company'])
            ->where('type', 'buses')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get()
            ->map(function ($scan) {
                $content = is_array($scan->content) ? $scan->content : json_decode($scan->content, true);

                $bus = $scan->bus ?? Bus::with('company')->where('name', $content['name'] ?? null)->first();

                $operator = $scan->user->name ?? null;
                $zone = $scan->user?->profile?->hotel?->zones?->first()?->name ?? null;

                // Distance
                $distance = null;
                if (!empty($scan->lat) && !empty($scan->lng)) {
                    $hotelLat = $scan->user?->profile?->hotel?->lat;
                    $hotelLng = $scan->user?->profile?->hotel?->lng;
                    if ($hotelLat && $hotelLng) {
                        $distance = formatDistance(distance(
                            (float)$scan->lat,
                            (float)$scan->lng,
                            (float)$hotelLat,
                            (float)$hotelLng
                        ));
                    }
                }

                $hotel = $scan->user?->profile?->hotel?->name ?? null;

                return [
                    'id' => $scan->id,
                    'bus_id' => $bus->id ?? null,
                    'bus_name' => $bus->name ?? $content['name'] ?? null,
                    'company' => $bus && $bus->company ? ['name' => $bus->company->name] : null,
                    'extra' => strtolower($scan->extra ?? 'none'),
                    'created_at' => $scan->created_at,
                    'operator' => $operator,
                    'hotel' => $hotel,
                    'zone' => $zone,
                    'distance' => $distance,
                ];
            });

        // Group scans by date
        $groupedByDate = $scans->groupBy(fn($s) => $s['created_at']->format('Y-m-d'));
        $result = [];

        foreach ($groupedByDate as $date => $scansOfDay) {
            $buses = [];

            $scansOfDay->groupBy('bus_name')->each(function ($busScans, $busName) use (&$buses) {
                // Group scans by operator + extra
                $groupedScans = $busScans->sortBy('created_at')
                    ->groupBy(fn($s) => $s['operator'] . '|' . $s['extra']);

                $scansForJs = [];
                $duplicatesCount = ['arrival' => 0, 'boarding' => 0, 'departure' => 0];
                $totals = ['arrival' => 0, 'boarding' => 0, 'departure' => 0];

                foreach ($groupedScans as $key => $scansGroup) {
                    $tempGroup = [];
                    $lastTime = null;

                    foreach ($scansGroup as $scan) {
                        if ($lastTime && $scan['created_at']->diffInMinutes($lastTime) < 15) {
                            $tempGroup[] = $scan; // duplicate
                        } else {
                            if (!empty($tempGroup)) {
                                $scansForJs[] = ['group' => $tempGroup, 'collapsed' => true];

                                $extraKey = $tempGroup[0]['extra'] ?? 'none';
                                $duplicatesCount[$extraKey] = ($duplicatesCount[$extraKey] ?? 0) + max(count($tempGroup) - 1, 0);
                                $totals[$extraKey] = ($totals[$extraKey] ?? 0) + 1;
                            }
                            $tempGroup = [$scan];
                        }
                        $lastTime = $scan['created_at'];
                    }

                    if (!empty($tempGroup)) {
                        $scansForJs[] = ['group' => $tempGroup, 'collapsed' => true];

                        $extraKey = $tempGroup[0]['extra'] ?? 'none';
                        $duplicatesCount[$extraKey] = ($duplicatesCount[$extraKey] ?? 0) + max(count($tempGroup) - 1, 0);
                        $totals[$extraKey] = ($totals[$extraKey] ?? 0) + 1;
                    }
                }

                $buses[] = [
                    'bus_name' => $busName,
                    'company' => $busScans->first()['company'] ?? null,
                    'total_arrivals' => $totals['arrival'] ?? 0,
                    'total_boardings' => $totals['boarding'] ?? 0,
                    'total_departures' => $totals['departure'] ?? 0,
                    'scans' => $scansForJs,
                    'duplicates' => $duplicatesCount,
                    'returned' => ($totals['departure'] ?? 0) > 1,
                ];
            });

            $buses = collect($buses)->sortByDesc('total_departures')->values()->toArray();

            $result[] = [
                'date' => $date,
                'buses' => $buses,
            ];
        }

        return view('statistics.bus-score', compact('start', 'end', 'result'));
    }

    public function operatorScore(Request $request) {
        $start = $request->start
            ? Carbon::parse($request->start)->startOfDay()
            : now()->subWeek()->startOfDay();
        $end = $request->end
            ? Carbon::parse($request->end)->endOfDay()
            : now()->endOfDay();

        // fetch scans with relations
        $scans = Scan::with(['user.profile.hotel.zones', 'bus.company'])
            ->where('type', 'buses')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get()
            ->map(function ($scan) {
                $content = is_array($scan->content) ? $scan->content : json_decode($scan->content, true);

                $bus = $scan->bus ?? Bus::with('company')->where('name', $content['name'] ?? null)->first();

                $operator = $scan->user->name ?? null;
                $zone = $scan->user?->profile?->hotel?->zones?->first()?->name ?? null;

                // distance from hotel
                $distance = null;
                if (!empty($scan->lat) && !empty($scan->lng)) {
                    $hotelLat = $scan->user?->profile?->hotel?->lat;
                    $hotelLng = $scan->user?->profile?->hotel?->lng;
                    if ($hotelLat && $hotelLng) {
                        $distance = formatDistance(distance(
                            (float) $scan->lat,
                            (float) $scan->lng,
                            (float) $hotelLat,
                            (float) $hotelLng
                        ));
                    }
                }

                $hotel = $scan->user?->profile?->hotel?->name ?? null;

                return [
                    'id' => $scan->id,
                    'user_id' => $scan->user?->id,
                    'bus_name' => $bus->name ?? $content['name'] ?? null,
                    'company' => $bus && $bus->company ? ['name' => $bus->company->name] : ['name' => '-'],
                    'extra' => strtolower($scan->extra ?? 'none'),
                    'created_at' => $scan->created_at,
                    'operator' => $operator ?? 'Unknown',
                    'hotel' => $hotel,
                    'zone' => $zone,
                    'distance' => $distance,
                ];
            });

        // group scans by date
        $groupedByDate = $scans->groupBy(fn($s) => $s['created_at']->format('Y-m-d'));
        $result = [];

        foreach ($groupedByDate as $date => $scansOfDay) {
            $operators = [];

            $scansOfDay->groupBy('operator')->each(function ($opScans, $operator) use (&$operators) {
                // group scans by bus+extra (to collapse duplicates)
                $groupedScans = $opScans->sortBy('created_at')
                    ->groupBy(fn($s) => ($s['bus_name'] ?? 'Unknown') . '|' . ($s['extra'] ?? 'none'));

                $scansForJs = [];
                $duplicatesCount = ['arrival' => 0, 'boarding' => 0, 'departure' => 0, 'none' => 0];
                $totals = ['arrival' => 0, 'boarding' => 0, 'departure' => 0, 'none' => 0];

                foreach ($groupedScans as $key => $scansGroup) {
                    $tempGroup = [];
                    $lastTime = null;

                    foreach ($scansGroup as $scan) {
                        if ($lastTime && $scan['created_at']->diffInMinutes($lastTime) < 15) {
                            $tempGroup[] = $scan; // duplicate
                        } else {
                            if (!empty($tempGroup)) {
                                $scansForJs[] = ['group' => $tempGroup, 'collapsed' => true];
                                $extraKey = $tempGroup[0]['extra'] ?? 'none';

                                if (!isset($duplicatesCount[$extraKey])) {
                                    $duplicatesCount[$extraKey] = 0;
                                    $totals[$extraKey] = 0;
                                }

                                $duplicatesCount[$extraKey] += max(count($tempGroup) - 1, 0);
                                $totals[$extraKey] += 1;
                            }
                            $tempGroup = [$scan];
                        }
                        $lastTime = $scan['created_at'];
                    }

                    if (!empty($tempGroup)) {
                        $scansForJs[] = ['group' => $tempGroup, 'collapsed' => true];
                        $extraKey = $tempGroup[0]['extra'] ?? 'none';

                        if (!isset($duplicatesCount[$extraKey])) {
                            $duplicatesCount[$extraKey] = 0;
                            $totals[$extraKey] = 0;
                        }

                        $duplicatesCount[$extraKey] += max(count($tempGroup) - 1, 0);
                        $totals[$extraKey] += 1;
                    }
                }

                $operators[] = [
                    'operator' => $operator ?? '-',
                    'operator_id' => $opScans->first()['user_id'] ?? null,
                    'hotel' => $opScans->first()['hotel'] ?? null,
                    'zone' => $opScans->first()['zone'] ?? null,
                    'total_arrivals' => $totals['arrival'] ?? 0,
                    'total_boardings' => $totals['boarding'] ?? 0,
                    'total_departures' => $totals['departure'] ?? 0,
                    'scans' => $scansForJs,
                    'duplicates' => $duplicatesCount,
                ];
            });

            $operators = collect($operators)->sortByDesc('total_departures')->values()->toArray();

            $result[] = [
                'date' => $date,
                'operators' => $operators,
            ];
        }

        return view('statistics.operator-score', compact('start', 'end', 'result'));
    }

    public function supervisorScore(Request $request) {
        $start = $request->start
            ? Carbon::parse($request->start)->startOfDay()
            : now()->subWeek()->startOfDay();
        $end = $request->end
            ? Carbon::parse($request->end)->endOfDay()
            : now()->endOfDay();

        // get supervisors (role 3 or 6)
        $supervisors = User::with('profile.zone')
            ->whereHas('profile', fn($q) => $q->whereIn('role_id', [3, 6]))
            ->get();

        // fetch scans
        $scans = Scan::with(['user.profile.hotel.zones', 'bus.company'])
            ->where('type', 'buses')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get()
            ->map(function ($scan) {
                $content = is_array($scan->content) ? $scan->content : json_decode($scan->content, true);

                $bus = $scan->bus ?? Bus::with('company')->where('name', $content['name'] ?? null)->first();

                $operator = $scan->user;
                $zone = $operator?->profile?->hotel?->zones?->first();

                $distance = null;
                if (!empty($scan->lat) && !empty($scan->lng) && $operator?->profile?->hotel) {
                    $hotelLat = $operator->profile->hotel->lat;
                    $hotelLng = $operator->profile->hotel->lng;
                    $distance = formatDistance(distance($scan->lat, $scan->lng, $hotelLat, $hotelLng));
                }

                return [
                    'id' => $scan->id,
                    'user_id' => $operator?->id,
                    'bus_name' => $bus->name ?? $content['name'] ?? null,
                    'company' => $bus && $bus->company ? ['name' => $bus->company->name] : ['name' => '-'],
                    'extra' => strtolower($scan->extra ?? 'none'),
                    'created_at' => $scan->created_at,
                    'operator' => $operator?->name,
                    'hotel' => $operator?->profile?->hotel?->name,
                    'zone' => $zone?->name,
                    'zone_id' => $zone?->id,
                    'distance' => $distance,
                ];
            });

        $resultByDate = [];

        // group scans by date
        foreach ($scans->groupBy(fn($s) => $s['created_at']->format('Y-m-d')) as $date => $scansOfDay) {
            $supervisorsOfDay = [];

            foreach ($supervisors as $sup) {
                $supZoneId = $sup->profile->zone_id;

                // only include scans in the supervisor's zone
                $supScans = $scansOfDay->filter(fn($scan) => $scan['zone_id'] == $supZoneId);

                if ($supScans->isEmpty()) continue;

                // collapse duplicates
                $groupedScans = $supScans->sortBy('created_at')
                    ->groupBy(fn($s) => ($s['bus_name'] ?? 'Unknown') . '|' . ($s['extra'] ?? 'none'));

                $scansForJs = [];
                $duplicatesCount = ['arrival' => 0, 'boarding' => 0, 'departure' => 0, 'none' => 0, 'newdeparture' => 0];
                $totals = ['arrival' => 0, 'boarding' => 0, 'departure' => 0, 'none' => 0, 'newdeparture' => 0];

                foreach ($groupedScans as $key => $scansGroup) {
                    $tempGroup = [];
                    $lastTime = null;

                    foreach ($scansGroup as $scan) {
                        if ($lastTime && $scan['created_at']->diffInMinutes($lastTime) < 15) {
                            $tempGroup[] = $scan;
                        } else {
                            if (!empty($tempGroup)) {
                                $scansForJs[] = ['group' => $tempGroup, 'collapsed' => true];
                                $extraKey = $tempGroup[0]['extra'] ?? 'none';
                                $duplicatesCount[$extraKey] += max(count($tempGroup) - 1, 0);
                                $totals[$extraKey] += 1;
                            }
                            $tempGroup = [$scan];
                        }
                        $lastTime = $scan['created_at'];
                    }

                    if (!empty($tempGroup)) {
                        $scansForJs[] = ['group' => $tempGroup, 'collapsed' => true];
                        $extraKey = $tempGroup[0]['extra'] ?? 'none';
                        $duplicatesCount[$extraKey] += max(count($tempGroup) - 1, 0);
                        $totals[$extraKey] += 1;
                    }
                }

                // map totals to keys expected by Blade
                $supervisorsOfDay[] = [
                    'supervisor' => $sup->name,
                    'supervisor_id' => $sup->id,
                    'zone' => $sup->profile->zone->name ?? '-',
                    'scans' => $scansForJs,
                    'duplicates' => $duplicatesCount,
                    'total_arrivals' => $totals['arrival'] ?? 0,
                    'total_boardings' => $totals['boarding'] ?? 0,
                    'total_departures' => $totals['departure'] ?? 0,
                ];
            }

            if (!empty($supervisorsOfDay)) {
                $resultByDate[] = [
                    'date' => $date,
                    'supervisors' => $supervisorsOfDay,
                ];
            }
        }

        return view('statistics.supervisor-score', [
            'start' => $start,
            'end' => $end,
            'result' => $resultByDate,
        ]);
    }

    public function companyScore(Request $request) {
        $start = $request->start
            ? Carbon::parse($request->start)->startOfDay()
            : now()->subWeek()->startOfDay();
        $end = $request->end
            ? Carbon::parse($request->end)->endOfDay()
            : now()->endOfDay();

        $scans = Scan::with(['user.profile.hotel', 'bus.company'])
            ->where('type', 'buses')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get()
            ->map(function ($scan) {
                $content = is_array($scan->content) ? $scan->content : json_decode($scan->content, true);
                $bus = $scan->bus ?? Bus::with('company')->where('name', $content['name'] ?? null)->first();

                return [
                    'id' => $scan->id,
                    'bus_name' => $bus->name ?? $content['name'] ?? null,
                    'company' => $bus && $bus->company ? ['name' => $bus->company->name] : null,
                    'extra' => strtolower($scan->extra ?? 'none'),
                    'created_at' => $scan->created_at,
                    'operator' => $scan->user->name ?? null,
                    'hotel' => $scan->user?->profile?->hotel?->name ?? null,
                ];
            });

        $groupedByDate = $scans->groupBy(fn($s) => $s['created_at']->format('Y-m-d'));
        $result = [];

        foreach ($groupedByDate as $date => $scansOfDay) {
            $companies = [];

            $scansOfDay->groupBy(fn($s) => $s['company']['name'] ?? '-')
                ->each(function ($compScans, $company) use (&$companies) {
                    $groupedScans = $compScans->sortBy('created_at')
                        ->groupBy(fn($s) => $s['bus_name'] . '|' . $s['extra']);

                    $scansForJs = [];
                    $duplicatesCount = ['arrival' => 0, 'boarding' => 0, 'departure' => 0];
                    $totals = ['arrival' => 0, 'boarding' => 0, 'departure' => 0];

                    foreach ($groupedScans as $scansGroup) {
                        $tempGroup = [];
                        $lastTime = null;

                        foreach ($scansGroup as $scan) {
                            if ($lastTime && $scan['created_at']->diffInMinutes($lastTime) < 15) {
                                $tempGroup[] = $scan;
                            } else {
                                if (!empty($tempGroup)) {
                                    $scansForJs[] = ['group' => $tempGroup, 'collapsed' => true];
                                    $extraKey = $tempGroup[0]['extra'] ?? 'none';
                                    $duplicatesCount[$extraKey] = ($duplicatesCount[$extraKey] ?? 0) + max(count($tempGroup) - 1, 0);
                                    $totals[$extraKey] = ($totals[$extraKey] ?? 0) + 1;
                                }
                                $tempGroup = [$scan];
                            }
                            $lastTime = $scan['created_at'];
                        }

                        if (!empty($tempGroup)) {
                            $scansForJs[] = ['group' => $tempGroup, 'collapsed' => true];
                            $extraKey = $tempGroup[0]['extra'] ?? 'none';
                            $duplicatesCount[$extraKey] = ($duplicatesCount[$extraKey] ?? 0) + max(count($tempGroup) - 1, 0);
                            $totals[$extraKey] = ($totals[$extraKey] ?? 0) + 1;
                        }
                    }

                    // ✅ count unique buses with at least one arrival/departure
                    $workedBuses = $compScans
                        ->filter(fn($s) => in_array($s['extra'], ['arrival', 'departure']))
                        ->pluck('bus_name')
                        ->unique()
                        ->count();

                    $companies[] = [
                        'company' => $company,
                        'worked_buses' => $workedBuses,
                        'total_arrivals' => $totals['arrival'] ?? 0,
                        'total_boardings' => $totals['boarding'] ?? 0,
                        'total_departures' => $totals['departure'] ?? 0,
                        'scans' => $scansForJs,
                        'duplicates' => $duplicatesCount,
                    ];
                });

            $companies = collect($companies)->sortByDesc('total_departures')->values()->toArray();

            $result[] = [
                'date' => $date,
                'companies' => $companies,
            ];
        }

        return view('statistics.company-score', compact('start', 'end', 'result'));
    }
}
