<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission {
    public function handle(Request $request, Closure $next, string $permission) {
        $user = Auth::user();

        if (!$user || !$user->profile || !in_array($permission, $user->profile->role->permissions ?? [])) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
