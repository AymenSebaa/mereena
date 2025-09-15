<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Hotel;
use Illuminate\Http\Request;

class GuestController extends Controller {
    public function index(Request $request) {
        return view('guests.index'); // no $guests here, fetched by AJAX
    }

    public function live(Request $request) {
        $query = self::getUserQuery($request->user());

        if (v($search = $request->string('search')->toString())) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhereHas('profile.hotel', fn($h) => $h->where('name', 'like', "%$search%"))
                    ->orWhereHas('profile.country', fn($c) => $c->where('name_en', 'like', "%$search%"));
            });
        }

        return response()->json($query->latest()->with(['profile.hotel', 'profile.country'])->get());
    }

    public static function getUserQuery($user) {
        $query = User::with(['reservations.status'])->whereHas('profile', fn($q) => $q->where('role_id', 10))
            ->with('profile');

        $profile = $user->profile ?? null;
        if ($profile && $profile->role_id == 10) {
            if ($profile->hotel_id) {
                // Restrict guests to same hotel
                $hotelExternalId = optional(Hotel::find($profile->hotel_id))->external_id;
                $query->whereHas('profile', fn($p) => $p->where('hotel_id', $profile->hotel_id));
            } else {
                $query->whereRaw('0=1'); // block if no hotel
            }
        }

        return $query;
    }
}
