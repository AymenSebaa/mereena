<?php

namespace Modules\World\Http\Controllers;

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

}
