<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Models\Plan;

class PlanController extends Controller {
    public function index() {
        $plans = Plan::all();
        return view('saas::plans.index', compact('plans'));
    }

    public function upsert(Request $request) {
        $id = $request->id;

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'slug'     => 'nullable|string|max:255|unique:plans,slug,' . $id,
            'price'    => 'required|numeric|min:0',
            'interval' => 'required|string|max:50',
            'features' => 'nullable|json',
        ]);

        $plan = Plan::updateOrCreate(['id' => $id], $validated);

        return response()->json([
            'result' => true,
            'message' => $id ? 'Plan updated successfully' : 'Plan created successfully',
            'data' => $plan,
        ]);
    }

    public function delete($id) {
        if ($plan = Plan::find($id)) $plan->delete();

        return response()->json(['result' => true, 'message' => 'Plan deleted']);
    }

}
