<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Hotel;
use App\Models\Zone;
use App\Models\Country;
use Illuminate\Http\Request;

class StaffController extends Controller {
    public function index(Request $request) {
        $user = $request->user();
        $profile = $user->profile;

        $staffQuery = User::with(['profile.role', 'profile.hotel', 'profile.zone', 'profile.country'])
            ->whereHas('profile', fn($q) => $q->where('role_id', '!=', 10)); // exclude Guests

        // Supervisor or Manager (role_id 3 or 6)
        if ($profile && in_array($profile->role_id, [3, 6]) && $profile->zone_id) {
            // Get hotel IDs in this zone
            $hotelIds = $profile->zone->hotels->pluck('id') ?? collect();

            // Only users assigned to those hotels, excluding Guests
            $staffQuery->whereHas('profile', function ($q) use ($hotelIds) {
                $q->whereIn('hotel_id', $hotelIds);
            });
        }

        $staff = $staffQuery->orderByDesc('id')->paginate(1200);

        $roles = Role::all();
        $hotels = Hotel::orderBy('name')->get();
        $zones = Zone::orderBy('name')->get();
        $countries = Country::orderBy('name_en')->get();

        return view('staff.index', compact('staff', 'roles', 'hotels', 'zones', 'countries'));
    }


    public function upsert(Request $request) {
        $request->validate([
            'id'         => ['nullable', 'exists:users,id'],
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email,' . $request->id],
            'role_id'    => ['required', 'exists:roles,id'],
            'country_id' => ['required', 'exists:countries,id'],
            'hotel_id'   => ['nullable', 'exists:hotels,id'],
            'zone_id'    => ['nullable', 'exists:zones,id'],
            'phone'      => ['nullable', 'string', 'max:20', 'unique:profiles,phone,' . optional(Profile::where('user_id', $request->id)->first())->id],
        ]);

        // Update or create the user
        $user = User::updateOrCreate(
            ['id' => v($request->id)],
            [
                'name'  => v($request->name),
                'email' => v($request->email),
            ]
        );

        // Update or create the profile
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);
        $profile->role_id    = v($request->role_id);
        $profile->country_id = v($request->country_id);
        $profile->phone      = v($request->phone);

        // Assign hotel or zone based on role
        $roleId = intval($request->role_id);

        $profile->hotel_id = null;
        $profile->zone_id  = null;

        if ($roleId === 3 || $roleId === 6) { // Supervisor => zone
            $profile->zone_id  = v($request->zone_id);
        } elseif ($roleId === 4) { // Operator => hotel
            $profile->hotel_id = v($request->hotel_id);
        }

        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Staff saved successfully',
            'user_id' => $user->id,
        ]);
    }


    public function destroy(User $user) {
        $user->delete();
        return response()->json(['success' => true, 'message' => 'Staff deleted successfully']);
    }
}
