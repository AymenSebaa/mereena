<?php

namespace App\Http\Controllers;

use App\Models\Site;

class SiteController extends BaseCrudController {
    protected string $modelClass = Site::class;
    protected string $viewPrefix = 'sites';

    protected function rules(): array {
        return [
            'name'     => 'required|string|max:255',
            'address'  => 'nullable|string|max:500',
            'lat'      => 'nullable|numeric',
            'lng'      => 'nullable|numeric',
            'geofence' => 'nullable|json',
        ];
    }

    protected function label(): string {
        return "Site";
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
