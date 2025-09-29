<?php

namespace Modules\World\Http\Controllers;

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

}
