<?php

namespace App\Http\Controllers;

use App\Mail\ScanMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\Company;
use App\Models\Hotel;
use App\Models\HotelZone;
use App\Models\Scan;
use App\Models\Type;
use App\Models\User;
use App\Models\Zone;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BusController extends Controller {
    public function index(Request $request) {
        $data['buses'] = []; // $this->getUserQuery($request->user())->latest()->paginate(12);
        $data['types'] = Type::where('name', 'Vehicules')->first()->subTypes ?? [];

        return view('buses.index', $data);
    }

    public function upsert(Request $request) {
        $data = $request->validate([
            'id' => 'nullable|exists:buses,id',
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:types,id',
        ]);

        $bus = Bus::updateOrCreate(
            ['id' => $data['id'] ?? null],
            [
                'name' => v($data['name']),
                'type_id' => v($data['type_id']),
            ]
        );

        return response()->json([
            'success' => true,
            'bus' => $bus->load('type'),
        ]);
    }

    public function fetch() {
        $url = env('DJAZ_BASE_URL') . '/get_devices?user_api_hash=' . env('DJAZFLEET_API_HASH');
        $proxyUrl = "https://odiro-dz.com/tfa/public/url";
        $response = Http::get($proxyUrl, ['url' => $url]);

        if ($response->ok()) {
            $count = 0;
            $type_id = Type::where('name', 'Vehicules')->first()
                ->subTypes->where('name', 'Bus')->first()->id;

            foreach ($response->json() as $group) {
                // Upsert company from group title
                $company = Company::updateOrCreate(
                    ['name' => trim($group['title'])],
                    []
                );

                foreach ($group['items'] as $d) {
                    Bus::updateOrCreate(
                        ['external_id' => v($d['id'])],
                        [
                            'name'       => v($d['name']) ?? null,
                            'lat'        => v($d['lat']) ?? null,
                            'lng'        => v($d['lng']) ?? null,
                            'status'     => v($d['online']) ?? null,
                            'type_id'    => $type_id,
                            'company_id' => $company->id,
                        ]
                    );
                    $count++;
                }
            }
            return "Buses fetched and saved successfully: $count";
        }

        return "Failed to fetch buses: " . $response->status();
    }

    // Bulk QR page
    public function bulkQRCodes(Request $request) {
        $ids = $request->query('ids');

        if ($ids) {
            $ids = explode(',', $ids);
            $buses = Bus::whereIn('id', $ids)->get();
        } else {
            $buses = Bus::all(); // no selection → all
        }

        return view('buses.bulk-qrcodes', compact('buses'));
    }

    // Single QR page
    public function singleQRCode(Bus $bus) {
        return view('buses.single-qrcode', compact('bus'));
    }

    // Live AJAX fetching (consistent with index filters)
    public function live(Request $request) {
        $buses = $this->getUserQuery($request->user());

        if ($search = $request->input('search')) {
            $buses->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Custom order: online > ack > offline
        $buses->orderByRaw("
            CASE status
                WHEN 'online' THEN 1
                WHEN 'ack' THEN 2
                WHEN 'offline' THEN 3
                ELSE 4
            END
        ");

        // Optional: fallback by latest
        // $buses->latest('updated_at');

        return response()->json($buses->get());
    }

    public static function getUserQuery($user) {
        $query = Bus::with(['scans.user', 'company']); // eager load scans with user
        $profile = $user->profile ?? null;

        if ($profile && in_array($profile->role_id, [3, 4, 6, 10])) {
            if ($profile->hotel_id) {
                $query->whereHas('tasks', function ($q) use ($profile) {
                    $q->where('hotel_id', $profile->hotel_id);
                });
            } else if ($profile->zone_id) {
                $zone = Zone::find($profile->zone_id);
                $hotelIds = $zone?->hotels->pluck('id') ?? collect();
                $query->whereHas('tasks', function ($q) use ($hotelIds) {
                    $q->whereIn('hotel_id', $hotelIds);
                });
            } else {
                $query->whereRaw('0 = 1');
            }
        }

        return $query;
    }

    public function listBuses() {
        return Bus::select('id', 'external_id', 'name', 'lat', 'lng', 'status')->get();
    }

    public function scan(Request $request) {
        $request->validate([
            'name'   => 'required|string',
            'type'   => 'required|in:buses',
            'extra'  => 'nullable|in:arrival,boarding,departure,none',
        ]);

        $scan = Scan::create([
            'user_id' => Auth::id(),
            'type'    => $request->type ?? null,
            'type_id' => $request->type_id ?? null,
            'extra'   => $request->extra ?? null,
            'content' => $request->content ?? null,
            'lat'     => $request->lat,
            'lng'     => $request->lng,
        ]);

        $this->notifyUsers($scan);

        return response()->json([
            'success' => true,
            'scan_id' => $scan->id,
            'message' => 'Scan saved successfully.'
        ]);
    }

    protected function notifyUsers($scan) {
        $operator = $scan->user;

        // Admin (1) & Manager (2) → get all
        $adminsManagers = User::whereHas('profile', fn($q) => $q->whereIn('role_id', [1, 2]))->get();
        foreach ($adminsManagers as $user) {
            Mail::to($user->email)->queue(new ScanMail($scan, $user->profile->role_id));
        }

        // Supervisors (3) & Dispatchers (6) → by zone
        $zoneIds = [];

        if ($operator->profile?->hotel_id) {
            $zoneIds = HotelZone::where('hotel_id', $operator->profile->hotel_id)->pluck('zone_id');
        }

        if ($zoneIds->isNotEmpty()) {
            $supDispatch = User::whereHas('profile', function ($q) use ($zoneIds) {
                $q->whereIn('role_id', [3, 6])->whereIn('zone_id', $zoneIds);
            })->get();

            foreach ($supDispatch as $user) {
                Mail::to($user->email)->queue(new ScanMail($scan, $user->profile->role_id));
            }
        }

        // Guests (10) → same hotel as operator
        if ($operator->profile?->hotel_id) {
            $guests = User::whereHas('profile', function ($q) use ($operator) {
                $q->where('role_id', 10)
                    ->where('hotel_id', $operator->profile->hotel_id);
            })->get();

            foreach ($guests as $user) {
                Mail::to($user->email)->queue(new ScanMail($scan, $user->profile->role_id));
            }
        }
    }

    public function decryptQr(Request $request) {
        $request->validate([
            'content' => 'required|string',
        ]);

        $content = $request->input('content');

        try {
            $decrypted = ScanController::decrypt128($content);
            $data = json_decode($decrypted, true);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 0,
                'message' => 'Invalid QR code',
            ], 422);
        }

        if (!isset($data['type']) || $data['type'] !== 'buses' || !isset($data['type_id'])) {
            return response()->json([
                'status'  => 0,
                'message' => 'QR code is not a valid bus',
            ], 422);
        }

        // Find the bus
        $bus = Bus::find($data['type_id']);
        if (!$bus) {
            return response()->json([
                'status'  => 0,
                'message' => 'Bus not found',
            ], 404);
        }

        // Find operator’s hotel
        $user = Auth::user();
        $sourceHotel = $user->profile->hotel ?? null;

        if (!$sourceHotel) {
            return response()->json([
                'status'  => 0,
                'message' => 'Operator has no hotel assigned',
            ], 422);
        }

        // Destination hotels filtered by keywords
        $destinations = Hotel::where(function ($q) {
            $q->where('name', 'like', '%roport%')
                ->orWhere('name', 'like', '%safex%')
                ->orWhere('name', 'like', '%cic%');
        })->get();

        return response()->json([
            'status'       => 1,
            'qr_content'   => $data,
            'bus'          => $bus,
            'source'       => $sourceHotel,
            'destinations' => $destinations,
        ]);
    }
}
