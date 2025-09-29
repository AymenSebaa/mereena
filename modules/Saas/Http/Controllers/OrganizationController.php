<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Models\Organization;

class OrganizationController extends Controller {
    public function index() {
        $organizations = Organization::all();
        return view('saas::organizations.index', compact('organizations'));
    }

    public function upsert(Request $request) {
        $id = $request->id;

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'slug'    => 'nullable|string|max:255|unique:organizations,slug,' . $id,
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        $organization = Organization::updateOrCreate(['id' => $id], $validated);

        return response()->json([
            'result' => true,
            'message' => $id ? 'Organization updated successfully' : 'Organization created successfully',
            'data' => $organization,
        ]);
    }

    public function delete($id) {
        if ($org = Organization::find($id)) $org->delete();

        return response()->json(['result' => true, 'message' => 'Organization deleted']);
    }

}
