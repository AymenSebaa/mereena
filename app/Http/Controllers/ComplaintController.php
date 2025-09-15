<?php

namespace App\Http\Controllers;

use App\Mail\ComplaintMail;
use App\Models\Complaint;
use App\Models\HotelZone;
use App\Models\Profile;
use App\Models\Type;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller {
    public function index(Request $request, $id = null) {
        if ($id) {
            $complaint = Complaint::with(['user', 'decider', 'type'])->findOrFail($id);
            return response()->json($complaint);
        }

        $data['complaints'] = self::getUserQuery($request->user())
            ->with(['user', 'decider', 'type'])
            ->latest()
            ->get();

        $data['types'] = Type::where('name', 'Complaints')
            ->first()
            ->subTypes ?? collect();

        $data['status'] = Type::where('name', 'Complaints status')
            ->first()
            ->subTypes ?? collect();

        return view('complaints.index', $data);
    }

    public function upsert(Request $request) {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'status_id' => ['nullable', 'exists:types,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        if ($request->id) {
            $complaint = Complaint::findOrFail($request->id);
            $message = 'Complaint updated successfully';
        } else {
            $complaint = new Complaint();
            $complaint->user_id = $user->id; // creator
            $message = 'Complaint created successfully';
        }

        $complaint->status_id  = v($request->status_id);

        // mark decision (only if status changed to approved/rejected)
        $status = Type::find($complaint->status_id);
        if ($status->name == 'Open') {
            $validator = Validator::make($request->all(), [
                'subject' => ['required', 'string', 'max:255'],
                'body'    => ['required', 'string'],
                'type_id' => ['nullable', 'exists:types,id'],
            ]);

            $complaint->type_id  = v($request->type_id);
            $complaint->subject  = v($request->subject);
            $complaint->body     = v($request->body);
        } else {
            $complaint->decided_by = $user->id;
            $complaint->decided_at = now();
        }

        $complaint->save();
        $this->notifyUsers($complaint, $request->id ? 'status_updated' : 'created');

        session()->flash('success', $message);
        return response()->json(['result' => $complaint]);
    }

    protected function notifyUsers(Complaint $complaint, string $event) {
        $guest = $complaint->user;

        // 1. Guest (role_id = 10)
        if ($guest->profile?->role_id == 10) {
            Mail::to($guest->email)->queue(new ComplaintMail($complaint, 10, $event));
        }

        // 2. Admins (1) & Managers (2)
        $adminsManagers = User::whereHas(
            'profile',
            fn($q) => $q->whereIn('role_id', [1, 2])
        )->get();

        foreach ($adminsManagers as $user) {
            Mail::to($user->email)->queue(new ComplaintMail($complaint, $user->profile->role_id, $event));
        }

        // 3. Supervisors (3) & Dispatchers (6) → find zone by hotel
        $hotelId = $guest->profile?->hotel_id;
        if ($hotelId) {
            // Get zone(s) linked to this hotel
            $zoneIds = HotelZone::where('hotel_id', $hotelId)->pluck('zone_id');

            // Supervisors & Dispatchers in same zone(s)
            $supDispatch = User::whereHas('profile', function ($q) use ($zoneIds) {
                $q->whereIn('role_id', [3, 6])->whereIn('zone_id', $zoneIds);
            })->get();

            foreach ($supDispatch as $user) {
                Mail::to($user->email)->queue(new ComplaintMail($complaint, $user->profile->role_id, $event));
            }

            // 4. Operators (4) with same hotel
            $operators = User::whereHas('profile', function ($q) use ($hotelId) {
                $q->where('role_id', 4)->where('hotel_id', $hotelId);
            })->get();

            foreach ($operators as $user) {
                Mail::to($user->email)->send(new ComplaintMail($complaint, 4, $event));
            }
        }
    }


    public function delete($id) {
        $complaint = Complaint::find($id);
        if ($complaint) {
            $complaint->delete();
            return back()->with('success', 'Complaint deleted successfully');
        }
        return back()->with('error', 'Complaint not found');
    }

    /**
     * Role-based query scoping
     */
    public static function getUserQuery($user) {
        $query = Complaint::query();
        $profile = $user->profile ?? null;

        if ($profile && in_array($profile->role_id, [3, 4, 6, 10])) {
            if ($profile->hotel_id) {
                // Get all users assigned to this hotel
                $userIds = $profile->hotel->profiles->pluck('user_id') ?? collect();
                $query->whereIn('user_id', $userIds);
            } elseif ($profile->zone_id) {
                // Get all hotels of this zone
                $zone = Zone::find($profile->zone_id);
                $hotelIds = $zone?->hotels->pluck('id') ?? collect();

                // Get all users assigned to these hotels
                $userIds = Profile::whereIn('hotel_id', $hotelIds)->pluck('user_id') ?? collect();
                $query->whereIn('user_id', $userIds);
            } else {
                // If user has no hotel/zone assigned, maybe return nothing or all? Adjust as needed
                $query->where('user_id', $user->id);
            }
        }
        // Admins, managers, etc. see all complaints (no filter)

        return $query;
    }
}
