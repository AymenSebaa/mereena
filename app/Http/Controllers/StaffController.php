<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Site;
use App\Models\Zone;
use Illuminate\Http\Request;
use Modules\World\Models\State;
use Modules\World\Models\Country;
class StaffController extends Controller {
    public function index(Request $request) {
        $user = $request->user();
        $profile = $user->profile;

        $staffQuery = Staff::with(['profile.role', 'profile.site', 'profile.zone', 'profile.state', 'profile.country'])
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

        $data['staff'] = $staffQuery->orderByDesc('id')->paginate(1200);

        $data['roles'] = Role::all();
        $data['sites'] = Site::orderBy('name')->get();
        $data['zones'] = Zone::orderBy('name')->get();
        $data['states'] = State::orderBy('name')->get();
        $data['countries'] = Country::orderBy('name')->get();

        return view('staff.index', $data);
    }


    public function upsert(Request $request) {
        $request->validate([
            'id'         => ['nullable', 'exists:users,id'],
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email,' . $request->id],
            'role_id'    => ['required', 'exists:roles,id'],
            'country_id' => ['required', 'exists:countries,id'],
            'state_id'   => ['nullable', 'exists:states,id'],
            'site_id'    => ['nullable', 'exists:sites,id'],
            'zone_id'    => ['nullable', 'exists:zones,id'],
            'phone'      => ['nullable', 'string', 'max:20', 'unique:profiles,phone,' . optional(Profile::where('user_id', $request->id)->first())->id],
        ]);

        // Update or create the user
        $staff = Staff::updateOrCreate(
            ['id' => v($request->id)],
            [
                'name'  => v($request->name),
                'email' => v($request->email),
            ]
        );

        // Update or create the profile
        $profile = Profile::firstOrCreate(['user_id' => $staff->id]);
        $profile->role_id    = v($request->role_id);
        $profile->country_id = v($request->country_id);
        $profile->phone      = v($request->phone);

        // Assign site or zone based on role
        $roleId = intval($request->role_id);

        $profile->site_id = null;
        $profile->zone_id  = null;
        $profile->state_id  = null;

        if ($roleId === 3 || $roleId === 6) { // Supervisor => zone
            $profile->zone_id  = v($request->zone_id);
        } elseif ($roleId === 4) { // Operator => site
            $profile->site_id = v($request->site_id);
        } elseif ($roleId === 13) { // Operator => site
            $profile->state_id = v($request->state_id);
        }

        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Staff saved successfully',
            'staff_id' => $staff->id,
        ]);
    }


    public function destroy(Staff $staff) {
        $staff->delete();
        return response()->json(['success' => true, 'message' => 'Staff deleted successfully']);
    }
}
