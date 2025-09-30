<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

function v($value) {
    if (is_null($value) || $value === '') return null;
    if (is_array($value)) return json_encode($value, JSON_UNESCAPED_UNICODE);
    // if (is_numeric($value)) return (int) $value; 

    return trim((string) $value);
}

function distance($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6371000;
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLng / 2) * sin($dLng / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
}

function formatDistance(float $meters): string {
    if ($meters < 1000) {
        return round($meters) . ' m';
    }
    return round($meters / 1000, 1) . ' km';
}

function installModule($module, $table, $seeder) {
    $migration  = "$table" . "_table";
    $migration_path =  "modules/$module/Database/Migrations/$migration.php";
    $seeder_class = "Modules\\$module\\Database\\Seeders\\$seeder";

    try {
        if (!Schema::hasTable($table)) {
            DB::table('migrations')->where('migration', 'like', "%{$table}%")->delete();
            Artisan::call('migrate', ['--path' => $migration_path, '--force' => true]);
        }
        if (!Schema::hasTable($table)) return response()->json(['status' => 'error', 'message' => "Migration failed for $table"]);
        if (DB::table($table)->count() > 0) return response()->json(['status' => 'info', 'message' => "$table already seeded"]);

        Artisan::call('db:seed', ['--class' => $seeder_class, '--force' => true]);
        return response()->json(['status' => 'success', 'message' => "$table installed successfully"]);
    } catch (Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function oRoute($name, $parameters = [], $absolute = true) {
    $slug = Auth::user()?->organization?->slug ?? 'admin';
    if (!is_array($parameters)) $parameters = [$parameters];
    $parameters = array_merge(['organization_slug' => $slug], $parameters);

    return route($name, $parameters, $absolute);
}

function udsRoutes($prefix, $controller, $namePrefix) {
    Route::prefix($prefix)->group(function () use ($controller, $namePrefix) {
        Route::get('/', [$controller, 'index'])->name("$namePrefix.index");
        Route::post('/upsert', [$controller, 'upsert'])->name("$namePrefix.upsert");
        Route::delete('/{id}', [$controller, 'delete'])->name("$namePrefix.delete");
        Route::post('/search', [$controller, 'search'])->name("$namePrefix.search");
    });
}