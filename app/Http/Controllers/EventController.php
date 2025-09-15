<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Hotel;
use App\Models\Zone;
use Carbon\Carbon;

class EventController extends Controller {
    public function index(Request $request) {
        $events = []; // $this->getUserQuery($request->user())->latest()->paginate(12);

        return view('events.index', compact('events'));
    }

    public function fetch() {
        $url = env('DJAZ_BASE_URL') . '/get_events?user_api_hash=' . env('DJAZFLEET_API_HASH');
        $proxyUrl = "https://odiro-dz.com/tfa/public/url";
        $response = Http::get($proxyUrl, ['url' => $url]);

        if ($response->ok()) {
            $json = $response->json();
            $count = 0;

            if (isset($json['items']['data'])) {
                foreach ($json['items']['data'] as $t) {
                    Event::updateOrCreate(
                        ['external_id' => v($t['id'])],
                        [
                            'user_id'     => v($t['user_id']) ?? null,
                            'device_id'   => v($t['device_id']) ?? null,
                            'geofence_id' => v($t['geofence_id']) ?? null,
                            'type'        => v($t['type']) ?? null,
                            'message'     => v($t['message']) ?? null,
                            'detail'      => v($t['detail']) ?? null,
                            'address'     => v($t['address']) ?? null,
                            'latitude'    => v($t['latitude']) ?? null,
                            'longitude'   => v($t['longitude']) ?? null,
                            'altitude'    => v($t['altitude']) ?? null,
                            'course'      => v($t['course']) ?? null,
                            'speed'       => v($t['speed']) ?? null,
                            'time'        => isset($t['time']) ? Carbon::createFromFormat('d-m-Y H:i:s', v($t['time'])) : null,
                            'additional'  => v($t['additional']) ?? null,
                        ]
                    );
                    $count++;
                }
            }

            return "Events fetched and saved successfully: $count";
        }

        return "Failed to fetch events: " . $response->status();
    }

    // Live AJAX fetching (consistent with index filters + search)
    public function live(Request $request) {
        $events = $this->getUserQuery($request->user());

        if ($search = $request->input('search')) {
            $events->where(function ($query) use ($search) {
                $query->where('message', 'like', "%{$search}%")
                    ->orWhere('detail', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('bus', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('hotel', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return response()->json($events->latest()->get());
    }

    /**
     * Helper: Apply the same filtering rules as in index()
     */
    public static function getUserQuery($user) {
        $query = Event::with(['bus', 'hotel']);
        $profile = $user->profile ?? null;

        if ($profile && in_array($profile->role_id, [3, 4, 6, 10])) {
            if ($profile->hotel_id) {
                $hotelExternalId = optional(Hotel::find($profile->hotel_id))->external_id;
                $query->where('geofence_id', $hotelExternalId);
            } else if ($profile->zone_id) {
                $zone = Zone::find($profile->zone_id);
                $hotelIds = $zone?->hotels->pluck('id') ?? collect();
                $query->whereIn('geofence_id', $hotelIds);
            } else {
                // No hotel assigned, return empty query
                $query->whereRaw('0 = 1');
            }
        }

        $query->where('type', '!=', 'overspeed');

        return $query;
    }
}
