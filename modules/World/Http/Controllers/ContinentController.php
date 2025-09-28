<?php

namespace Modules\World\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exception;
use Modules\World\Database\Seeders\ContinentSeeder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\World\Models\Continent;

class ContinentController extends Controller {
    /**
     * Display a listing of continents.
     */
    public function index() {
        $continents = Continent::all();
        return view('world::continents.index', compact('continents'));
    }

    /**
     * Store or update a continent.
     */
    public function upsert(Request $request, $id = null) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $continent = Continent::updateOrCreate(
            ['id' => $request->input('id') ?? $id],
            $validated
        );

        return response()->json([
            'result'    => true,
            'message'   => $request->input('id') ? 'Continent updated successfully' : 'Continent created successfully',
            'continent' => $continent,
        ]);
    }

    /**
     * Remove the specified continent.
     */
    public function delete($id) {
        $continent = Continent::find($id);
        if ($continent) $continent->delete();

        return response()->json([
            'result'  => true,
            'message' => 'Continent deleted successfully',
            'id'      => $id,
        ]);
    }

    public function install() {
        $module = 'World';
        $table  = 'continents';
        $migration  = "$table" . "_table";
        $migration_path =  "Modules/$module/Database/Migrations/$migration.php";

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

            if (Continent::count() > 0) {
                return response()->json(['status' => 'info', 'message' => 'Continents already installed']);
            }

            Artisan::call('db:seed', [
                '--class' => ContinentSeeder::class,
                '--force' => true,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Continents installed successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
