<?php

namespace Modules\World\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exception;
use Modules\World\Database\Seeders\RegionSeeder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\World\Models\Region;

class RegionController extends Controller {
    /**
     * Display a listing of regions.
     */
    public function index() {
        $regions = Region::with('continent')->get();
        return view('world::regions.index', compact('regions'));
    }

    /**
     * Store or update a region.
     */
    public function upsert(Request $request, $id = null) {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'm49_code'     => 'required|integer',
            'continent_id' => 'required|exists:continents,id',
        ]);

        $region = Region::updateOrCreate(
            ['id' => $request->input('id') ?? $id],
            $validated
        );

        return response()->json([
            'result' => true,
            'message' => $request->input('id')
                ? 'Region updated successfully'
                : 'Region created successfully',
            'region' => $region->load('continent'),
        ]);
    }

    /**
     * Remove the specified region.
     */
    public function delete($id) {
        $region = Region::find($id);
        if ($region) $region->delete();

        return response()->json([
            'result'  => true,
            'message' => 'Region deleted successfully',
            'id'      => $id,
        ]);
    }

    public function install() {
        $module = 'World';
        $table  = 'regions';
        $migration  = "$table" . "_table";
        $migration_path =  "modules/$module/Database/Migrations/$migration.php";

        try {
            // Run migration if table does not exist
            if (!Schema::hasTable($table)) {
                DB::table('migrations')->where('migration', 'like', $migration)->delete();

                Artisan::call('migrate', [
                    '--path' => $migration_path,
                    '--force' => true,
                ]);
            }

            if (!Schema::hasTable($table)) {
                return response()->json(['status' => 'error', 'message' => "Migration failed: Table $table still does not exist"]);
            }

            if (Region::count() > 0) {
                return response()->json(['status' => 'info', 'message' => 'Regions already installed']);
            }

            Artisan::call('db:seed', [
                '--class' => RegionSeeder::class,
                '--force' => true,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Regions installed successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
