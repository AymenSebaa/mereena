<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckPermission {
    public function handle(Request $request, Closure $next, string $permission) {
        $user = Auth::user();

        if (!$user || !$user->profile || !$user->profile->role) {
            abort(403, 'Unauthorized');
        }

        // Get user's permissions as a nested array (JSON decoded)
        $permissions = $user->profile->role->permissions;
        $allowedModules = collect($permissions);

        // Flatten nested keys to check if $permission exists
        $flattened = $this->flattenPermissions($allowedModules);

        if (!in_array($permission, $flattened)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }

    /**
     * Flatten nested permission structure into simple keys
     * e.g. ["stock" => ["inventories"=>{}, "products"=>{}]] 
     * becomes ["stock", "stock.inventories", "stock.products"]
     */
    protected function flattenPermissions($modules, $prefix = '') {
        $result = [];

        foreach ($modules as $key => $value) {
            $fullKey = $prefix ? $prefix . '.' . $key : $key;
            $result[] = $fullKey;

            if (is_array($value) && count($value) > 0) {
                $result = array_merge($result, $this->flattenPermissions($value, $fullKey));
            }
        }

        return $result;
    }
}
