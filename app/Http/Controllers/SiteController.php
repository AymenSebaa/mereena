<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller {
    public function index(Request $request) {
        $isAjax = $request->ajax() || $request->wantsJson();

        if ($isAjax) {
            $query = $this->getUserQuery($request->user());

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            }

            $sites = $query->latest()->get();
            return response()->json($sites);
        }

        return view('sites.index');
    }

    public function upsert(Request $request, $id = null) {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'lat'     => 'nullable|numeric',
            'lng'     => 'nullable|numeric',
            'geofence' => 'nullable|json'
        ]);

        $site = Site::updateOrCreate(
            ['id' => $request->input('id') ?? $id],
            $validated
        );

        return response()->json([
            'status'  => 'success',
            'message' => $request->input('id') 
                ? "Site $site->name updated successfully" 
                : "Site $site->name created successfully",
            'data'    => $site,
        ]);
    }

    public function delete($id) {
        $site = Site::find($id);
        if ($site) $site->delete();

        return response()->json([
            'status'  => 'success',
            'message' => "Site $site->name deleted successfully",
            'id'      => $id,
        ]);
    }

    public static function getUserQuery($user) {
        $query = Site::query();
        $profile = $user->profile ?? null;

        if ($profile && in_array($profile->role_id, [3, 4, 6, 10])) {
            if ($profile->site_id) {
                $query->where('id', $profile->site_id);
            } else if ($profile->zone_id) {
                $siteIds = $profile->zone?->sites->pluck('id') ?? collect();
                $query->whereIn('id', $siteIds);
            } else {
                $query->whereRaw('0 = 1'); // block if no site
            }
        }

        return $query;
    }
}
