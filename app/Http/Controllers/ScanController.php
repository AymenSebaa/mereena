<?php

namespace App\Http\Controllers;

use App\Mail\ScanMail;
use App\Models\HotelZone;
use Illuminate\Http\Request;
use App\Models\Scan;
use App\Models\User;
use App\Models\Zone;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ScanController extends Controller {

    public function index(Request $request) {
        if ($request->ajax()) {
            $query = self::getUserQuery($request->user())
                ->with([
                    'user.profile.role',
                    'user.profile.hotel',
                    'user.profile.hotel.zones',
                    'bus',
                    'hotel',
                    'guest',
                ])
                ->orderBy('created_at', 'desc');

            // --- Date filter ---
            $from = $request->get('from')
                ? Carbon::parse($request->get('from'))->startOfDay()
                : now()->startOfDay();

            $to = $request->get('to')
                ? Carbon::parse($request->get('to'))->endOfDay()
                : now()->endOfDay();

            $query->whereBetween('created_at', [$from, $to]);
            return $query->get();
        }

        return view('scans.index');
    }

    public function indexDEV(Request $request) {
        if ($request->ajax()) {
            $query = self::getUserQuery($request->user())
                ->with([
                    'user.profile.role',
                    'user.profile.hotel',
                    'bus',
                    'hotel',
                    'guest',
                ])
                ->orderBy('created_at', 'desc');

            // --- Date filter ---
            $from = $request->get('from')
                ? Carbon::parse($request->get('from'))->startOfDay()
                : now()->startOfDay();

            $to = $request->get('to')
                ? Carbon::parse($request->get('to'))->endOfDay()
                : now()->endOfDay();

            $query->whereBetween('created_at', [$from, $to]);
            return $query->get();
        }

        return view('scans.indexDEV');
    }

    /**
     * Get scans query filtered by user profile, hotel, zone.
     */
    public static function getUserQuery($user) {
        $query = Scan::query(); // base query
        $profile = $user->profile ?? null;

        if ($profile && in_array($profile->role_id, [3, 4, 6, 10])) {
            if ($profile->hotel_id) {
                $query->where(function ($q) use ($profile) {
                    $q->where(function ($q1) use ($profile) {
                        $q1->where('type', 'hotels')
                            ->where('type_id', $profile->hotel_id);
                    })->orWhere('user_id', $profile->user_id); // include user's own scans
                });
            } elseif ($profile->zone_id) {
                $zone = Zone::find($profile->zone_id);
                $hotelIds = $zone?->hotels->pluck('id') ?? collect();
                $query->where(function ($q) use ($hotelIds, $profile) {
                    $q->where(function ($q1) use ($hotelIds) {
                        $q1->where('type', 'hotels')
                            ->whereIn('type_id', $hotelIds);
                    })->orWhere('user_id', $profile->user_id);
                });
            } else {
                $query->where('user_id', $profile->user_id);
            }
        }

        return $query;
    }

    // === preview QR content (decrypt if needed, return display info) ===
    public function preview(Request $request) {
        $content = $request->input('content');
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            try {
                $decrypted = self::decrypt128($content);
                $data = json_decode($decrypted, true);
            } catch (Exception $e) {
                return response()->json(['name' => 'Unknown', 'type' => 'unknown']);
            }
        }

        return response()->json([
            'name' => $data['name'] ?? 'Unknown',
            'type' => $data['type'] ?? 'unknown',
        ]);
    }

    // === Store Scan ===
    public function store(Request $request) {
        $request->validate([
            'extra'   => 'nullable|string',
            'content' => 'required|string',
            'lat'     => 'nullable|numeric',
            'lng'     => 'nullable|numeric',
        ]);

        $data = null;
        $decrypted = null;

        try {
            $data = json_decode($request->content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $decrypted = self::decrypt128($request->content);
                $data = json_decode($decrypted, true);
                if (!$data) return response()->json(['success' => false, 'message' => 'Invalid QR content'], 422);
            } else {
                $decrypted = $request->content;
            }

            $content = $decrypted;
            $content = [
                'name'    => $data['name'] ?? null,
                'type'    => $data['type'] ?? null,
                'type_id' => $data['type_id'] ?? null,
            ];

            $scan = Scan::create([
                'user_id' => Auth::id(),
                'type'    => $data['type'] ?? null,
                'type_id' => $data['type_id'] ?? null,
                'extra'   => $request->extra ?? null,
                'content' => $content,
                'lat'     => $request->lat,
                'lng'     => $request->lng,
            ]);

            $this->notifyUsers($scan);

            return response()->json([
                'success' => true,
                'scan_id' => $scan->id,
                'message' => 'Scan saved successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    protected function notifyUsers($scan) {
        $scan->content = json_decode($scan->content, true);
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

    // === AES-128-CBC ENCRYPTION / DECRYPTION ===
    protected static string $cipher = 'AES-128-CBC';

    public static function encrypt128(string $plain): string {
        $key = substr(config('app.key'), 0, 16);
        $iv = random_bytes(openssl_cipher_iv_length(self::$cipher));
        $cipherText = openssl_encrypt($plain, self::$cipher, $key, OPENSSL_RAW_DATA, $iv);
        if (!$cipherText) throw new Exception('Encryption failed.');
        return base64_encode($iv . $cipherText);
    }

    public static function decrypt128(string $payload): string {
        $key = substr(config('app.key'), 0, 16);
        $raw = base64_decode($payload, true);
        if (!$raw) throw new Exception('Base64 decode failed.');
        $ivLength = openssl_cipher_iv_length(self::$cipher);
        if (strlen($raw) <= $ivLength) throw new Exception('Payload too short.');
        $iv = substr($raw, 0, $ivLength);
        $cipherText = substr($raw, $ivLength);
        $plain = openssl_decrypt($cipherText, self::$cipher, $key, OPENSSL_RAW_DATA, $iv);
        if ($plain === false) throw new Exception('Decryption failed.');
        return $plain;
    }
}
