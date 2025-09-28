<?php

namespace Modules\World\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exception;
use Modules\World\Database\Seeders\CountrySeeder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\World\Models\Country;

class CountryController extends Controller {

    public function index() {
        $countries = Country::with('region.continent')->get();
        return view('world::countries.index', compact('countries'));
    }

    public function upsert(Request $request, $id = null) {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'iso2'      => 'required|string|size:2',
            'iso3'      => 'required|string|size:3',
            'phone_code' => 'nullable|string',
            'currency'  => 'nullable|string',
            'emoji'     => 'nullable|string|max:4',
            'lat'       => 'nullable|numeric',
            'lng'       => 'nullable|numeric',
        ]);

        $country = Country::updateOrCreate(
            ['id' => $request->input('id') ?? $id],
            $validated
        );

        return response()->json([
            'result'  => true,
            'message' => $request->input('id')
                ? 'Country updated successfully'
                : 'Country created successfully',
            'country' => $country->load('region.continent'),
        ]);
    }

    public function delete($id) {
        $country = Country::find($id);
        if ($country) $country->delete();

        return response()->json([
            'result'  => true,
            'message' => 'Country deleted successfully',
            'id'      => $id,
        ]);
    }

    public function install() {
        $module = 'World';
        $table  = 'countries';
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

            if (Country::count() > 0) {
                return response()->json(['status' => 'info', 'message' => 'Countries already installed']);
            }

            Artisan::call('db:seed', [
                '--class' => CountrySeeder::class,
                '--force' => true,
            ]);


            return response()->json(['status' => 'success', 'message' => 'Countries installed successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
