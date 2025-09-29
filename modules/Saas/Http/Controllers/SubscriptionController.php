<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Models\Subscription;

class SubscriptionController extends Controller {
    public function index() {
        $subscriptions = Subscription::with(['organization', 'plan', 'invoices'])->get();
        return view('saas::subscriptions.index', compact('subscriptions'));
    }

    public function upsert(Request $request) {
        $id = $request->id;

        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'plan_id'         => 'required|exists:plans,id',
            'status'          => 'required|string|max:50',
            'starts_at'       => 'nullable|date',
            'ends_at'         => 'nullable|date',
        ]);

        $subscription = Subscription::updateOrCreate(['id' => $id], $validated);

        return response()->json([
            'result' => true,
            'message' => $id ? 'Subscription updated successfully' : 'Subscription created successfully',
            'data' => $subscription,
        ]);
    }

    public function delete($id) {
        if ($sub = Subscription::find($id)) $sub->delete();

        return response()->json(['result' => true, 'message' => 'Subscription deleted']);
    }

}
