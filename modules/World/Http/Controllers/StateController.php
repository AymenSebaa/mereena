<?php

namespace Modules\World\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\World\Models\State;

class StateController extends Controller {

    public function index() {
        $states = State::with('country.region.continent')->get();
        return view('world::states.index', compact('states'));
    }

    public function upsert(Request $request, $id = null) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'iso2' => 'nullable|string|max:10',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        $state = State::updateOrCreate(
            ['id' => $request->input('id') ?? $id],
            $validated
        );

        return response()->json([
            'result' => true,
            'message' => $request->input('id') ? 'State updated successfully' : 'State created successfully',
            'state' => $state->load('country.region.continent'),
        ]);
    }

    public function delete($id) {
        $state = State::find($id);
        if ($state) $state->delete();

        return response()->json([
            'result' => true,
            'message' => 'State deleted successfully',
            'id' => $id,
        ]);
    }

    public function search(Request $request) {
        $q = $request->q;
        $cities = State::with('states')
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
