<?php

namespace Modules\World\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exception;
use Modules\World\Database\Seeders\CitySeeder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\World\Models\City;

class CityController extends Controller {

    public function index() {
        $cities = City::with('state.country.region.continent')->get();
        return view('world::cities.index', compact('cities'));
    }

    public function upsert(Request $request, $id = null) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
            'zip_code' => 'nullable|string|max:20',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        $city = City::updateOrCreate(
            ['id' => $request->input('id') ?? $id],
            $validated
        );

        return response()->json([
            'result' => true,
            'message' => $request->input('id') ? 'City updated successfully' : 'City created successfully',
            'city' => $city->load('state'),
        ]);
    }

    public function delete($id) {
        $city = City::find($id);
        if ($city) $city->delete();

        return response()->json([
            'result' => true,
            'message' => 'City deleted successfully',
            'id' => $id,
        ]);
    }

    public function install() {
        $module = 'World';
        $table  = 'cities';
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

            if (City::count() > 0) {
                return response()->json(['status' => 'info', 'message' => 'Cities already installed']);
            }

            Artisan::call('db:seed', [
                '--class' => CitySeeder::class,
                '--force' => true,
            ]);


            return response()->json(['status' => 'success', 'message' => 'Cities installed successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
