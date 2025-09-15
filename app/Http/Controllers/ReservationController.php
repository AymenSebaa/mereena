<?php

namespace App\Http\Controllers;

use App\Mail\ReservationMail;
use App\Models\HotelZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\Type;
use App\Models\User;
use App\Models\Zone;
use App\Services\UserRegistrationService;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller {
    protected $registrationService;

    public function __construct(UserRegistrationService $registrationService) {
        $this->registrationService = $registrationService;
    }

    public function index(Request $request) {
        $data = FlightController::fetch();

        $data['reservations'] = [];

        $data['types'] = Type::where('name', 'Reservations')->first()?->subTypes ?? [];

        return view('reservations.index', $data);
    }

    public function live(Request $request) {
        $reservations = $this->getUserQuery($request->user())->with(['user', 'type', 'status', 'editor']);

        if ($search = $request->input('search')) {
            $reservations->where(function ($q) use ($search) {
                $q->where('note', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('status', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        return response()->json($reservations->latest()->get());
    }


    public static function getUserQuery($user) {
        $query = Reservation::query()->with(['user.profile.hotel', 'type', 'status']);
        $profile = $user->profile ?? null;

        if (!$profile) {
            // no profile → no access
            return $query->whereRaw('0 = 1');
        }

        switch ($profile->role_id) {
            case 1: // Admin
            case 2: // Manager
                // Access to all reservations
                return $query;

            case 10: // Guest
                // Only own reservations
                return $query->where('user_id', $user->id);

            case 6: // Dispatcher
                if ($profile->zone_id) {
                    // Get all hotels in dispatcher's zone
                    $hotelIds = HotelZone::where('zone_id', $profile->zone_id)->pluck('hotel_id');
                    return $query->whereHas('user.profile', function ($q) use ($hotelIds) {
                        $q->whereIn('hotel_id', $hotelIds);
                    });
                }
                return $query->whereRaw('0 = 1'); // no zone → no access

            default:
                return $query->whereRaw('0 = 1'); // other roles → no access
        }
    }


    public function delete(Reservation $reservation) {
        $reservation->delete();
        return response()->json(['success' => true]);
    }

    public function reserve(Request $request) {
        // Determine user
        if (Auth::check()) {
            $user = Auth::user(); // use logged-in user
        } else {
            $request->validate([
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'country_id' => ['required', 'exists:countries,id'],
                'hotel_id'   => ['required', 'exists:hotels,id'],
                'category'   => ['required', 'string', 'max:255'],
                'other_category' => ['required_if:category,Other', 'nullable', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:20', 'unique:profiles,phone'],
            ]);
            $user = $this->registrationService->register($request); // register new user
        }

        // Get parent "Reservations" type
        $type = Type::where('name', 'Reservations')->first()
            ->subTypes()->where('name', 'Flight')->first();

        // Get status "Pending"
        $status = Type::where('name', 'Reservations status')->first()
            ->subTypes()->where('name', 'Pending')->first();

        // Then create reservation
        $reservation = $user->reservations()->create([
            'type_id'     => v($type->id),
            'status_id'   => v($status->id),
            'pickup_time' => v($request->pickup_time),
            'content'     => v($request->flight),
        ]);

        $this->notifyRoles($reservation, 'created');

        return redirect()->route('reservations.index');
    }


    protected function notifyRoles(Reservation $reservation, string $event) {
        $guest = $reservation->user;

        // 1. Admin (1) & Manager (2) → notify all
        $adminsManagers = User::whereHas('profile', fn($q) => $q->whereIn('role_id', [1, 2, 6]))->get();
        foreach ($adminsManagers as $user) {
            Mail::to($user->email)->queue(new ReservationMail($reservation, $user->profile->role_id, $event));
        }

        // 2. Dispatchers (6) → only those in the same zone as reservation's hotel
        $hotelId = $guest?->profile?->hotel_id;
        if ($hotelId) {
            $zoneIds = HotelZone::where('hotel_id', $hotelId)->pluck('zone_id');

            $dispatchers = User::whereHas('profile', function ($q) use ($zoneIds) {
                $q->where('role_id', 6)->whereIn('zone_id', $zoneIds);
            })->get();

            foreach ($dispatchers as $user) {
                Mail::to($user->email)->queue(new ReservationMail($reservation, 6, $event));
            }
        }

        // 3. Guest (10) → only himself
        if ($guest) {
            Mail::to($guest->email)->queue(new ReservationMail($reservation, 10, $event));
        }
    }


    public function approve(Reservation $reservation) {
        $approved = Type::where('name', 'Reservations status')->first()
            ->subTypes()->where('name', 'Approved')->first();

        if ($approved) {
            $reservation->update([
                'status_id' => $approved->id,
                'edited_by' => Auth::id(),
                'edited_at' => now(),
            ]);
        }

        $this->notifyRoles($reservation, 'approved');

        return response()->json(['success' => true, 'reservation' => $reservation->load('status')]);
    }

    public function reject(Request $request, Reservation $reservation) {
        $rejected = Type::where('name', 'Reservations status')->first()
            ->subTypes()->where('name', 'Rejected')->first();

        if ($rejected) {
            $reservation->update([
                'status_id' => $rejected->id,
                'edited_by' => Auth::id(),
                'edited_at' => now(),
                'note' => v($request->note),
            ]);
        }

        $this->notifyRoles($reservation, 'rejected');

        return response()->json(['success' => true, 'reservation' => $reservation->load('status')]);
    }

    public function import(Request $request) {
        foreach ($request->reservations as $r) {
            $user = User::where('name', $r['name'])->first();
            if (!$user) {
                // optionally create new user
                continue;
            }

            $type = Type::where('name', 'Reservations')->first()?->subTypes()->where('name', 'Flight')->first();
            $status = Type::where('name', 'Reservations status')->first()?->subTypes()->where('name', 'Pending')->first();

            $user->reservations()->create([
                'type_id' => $type->id,
                'status_id' => $status->id,
                'pickup_time' => $r['pickup'],
                'content' => json_encode([
                    'flightNumber' => $r['flight'],
                    'returnDate' => $r['returnDate'],
                ])
            ]);
        }

        return response()->json(['success' => true]);
    }

    // UserController.php
    public function users(Request $request) {
        $exists = User::where('name', $request->name)->exists();
        return response()->json(['exists' => $exists]);
    }
}
