<?php

namespace Modules\World\Http\Controllers;

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

}
