<?php

namespace Modules\World\Http\Controllers;

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

    public function search(Request $request) {
        $q = $request->q;
        $cities = City::with('state')
            ->where('name', 'like', "%{$q}%")
            ->limit(20)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'state_id' => $c->state_id,
                'state_name' => $c->state->name ?? null
            ]);

        return response()->json($cities);
    }
}
