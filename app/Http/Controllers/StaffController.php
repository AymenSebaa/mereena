<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Site;
use App\Models\Zone;
use App\Models\Country;
use Illuminate\Http\Request;

class StaffController extends Controller {
    public function index(Request $request) {
        $user = $request->user();
        $profile = $user->profile;

        $staffQuery = User::with(['profile.role', 'profile.site', 'profile.zone', 'profile.country'])
            ->whereHas('profile', fn($q) => $q->where('role_id', '!=', 10)); // exclude Guests

        // Supervisor or Manager (role_id 3 or 6)
        if ($profile && in_array($profile->role_id, [3, 6]) && $profile->zone_id) {
            // Get site IDs in this zone
            $siteIds = $profile->zone->sites->pluck('id') ?? collect();

            // Only users assigned to those sites, excluding Guests
            $staffQuery->whereHas('profile', function ($q) use ($siteIds) {
                $q->whereIn('site_id', $siteIds);
            });
        }

        $staff = $staffQuery->orderByDesc('id')->paginate(1200);

        $roles = Role::all();
        $sites = Site::orderBy('name')->get();
        $zones = Zone::orderBy('name')->get();
        $countries = Country::orderBy('name')->get();

        return view('staff.index', compact('staff', 'roles', 'sites', 'zones', 'countries'));
    }


    public function upsert(Request $request) {
        $request->validate([
            'id'         => ['nullable', 'exists:users,id'],
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email,' . $request->id],
            'role_id'    => ['required', 'exists:roles,id'],
            'country_id' => ['required', 'exists:countries,id'],
            'site_id'   => ['nullable', 'exists:sites,id'],
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

        // Assign site or zone based on role
        $roleId = intval($request->role_id);

        $profile->site_id = null;
        $profile->zone_id  = null;

        if ($roleId === 3 || $roleId === 6) { // Supervisor => zone
            $profile->zone_id  = v($request->zone_id);
        } elseif ($roleId === 4) { // Operator => site
            $profile->site_id = v($request->site_id);
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
