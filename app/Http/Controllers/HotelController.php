<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Hotel;
use App\Models\Zone;

class HotelController extends Controller {
    public function index(Request $request) {
        // initial load → empty array (JS will populate via live AJAX)
        $hotels = [];
        return view('hotels.index', compact('hotels'));
    }

    // Live AJAX fetching (with role-based filters)
    public function live(Request $request) {
        $query = $this->getUserQuery($request->user());

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $hotels = $query->latest()->get();
        return response()->json($hotels);
    }

    // Bulk QR Codes page
    public function bulkQRCodes(Request $request) {
        $hotels = $this->getUserQuery($request->user())->latest()->get();
        return view('hotels.bulk-qrcodes', compact('hotels'));
    }

    // Single QR Code page
    public function singleQRCode(Hotel $hotel) {
        return view('hotels.single-qrcode', compact('hotel'));
    }

    public function fetch() {
        $url = env('DJAZ_BASE_URL') . '/get_geofences?user_api_hash=' . env('DJAZFLEET_API_HASH');
        $proxyUrl = "https://odiro-dz.com/tfa/public/url";
        $response = Http::get($proxyUrl, ['url' => $url]);

        if (!$response->ok()) {
            return "Failed to fetch hotels: " . $response->status();
        }

        // Safely get geofences array
        $geofences = data_get($response->json(), 'items.geofences', []);
        $count = 0;

        foreach ($geofences as $h) {
            $coords = [];
            if (!empty(v($h['coordinates']))) {
                $coords = is_string(v($h['coordinates']))
                    ? json_decode(v($h['coordinates']), true)
                    : v($h['coordinates']);
            }

            // Update or create hotel
            Hotel::updateOrCreate(
                ['external_id' => v($h['id'])],
                [
                    'name'     => v($h['name']),
                    'lat'      => data_get($h, 'center.lat'),
                    'lng'      => data_get($h, 'center.lng'),
                    'geofence' => json_encode($coords), // Store as JSON string
                    'address'  => v($h['address'] ?? '-'),
                    'stars'    => v($h['stars'] ?? 0), // optional
                ]
            );

            $count++;
        }


        return "Hotels fetched and saved successfully: $count";
    }


    public static function getUserQuery($user) {
        $query = Hotel::with(['scans.user']); // eager load scans with user
        $profile = $user->profile ?? null;

        if ($profile && in_array($profile->role_id, [3, 4, 6, 10])) {
            if ($profile->hotel_id) {
                $query->where('id', $profile->hotel_id);
            } else if ($profile->zone_id) {
                $zone = Zone::find($profile->zone_id);
                $hotelIds = $zone?->hotels->pluck('id') ?? collect();
                $query->whereIn('id', $hotelIds);
            } else {
                $query->whereRaw('0 = 1'); // block if no hotel
            }
        }

        return $query;
    }

    public function listHotels() {
        return Hotel::select('id', 'name', 'address', 'lat', 'lng')->get();
    }
}
