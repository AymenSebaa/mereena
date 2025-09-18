<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Zone;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZoneController extends Controller {
    public function index(Request $request) {
        $data['zones'] = Zone::withCount('hotels')->with('type')->get();

        // You might also want types for dropdowns (only type "Zones")
        $data['types'] = Type::where('name', 'Zones')->first()->subTypes ?? [];

        return view('zones.index', $data);
    }

    public function upsert(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'boolean'],
            'type_id' => ['nullable', 'exists:types,id'],
            'location' => ['nullable', 'string'],
            'geofence' => ['nullable', 'json'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        if ($request->id) {
            $zone = Zone::findOrFail($request->id);
            $message = 'Zone updated successfully';
        } else {
            $zone = new Zone();
            $message = 'Zone created successfully';
        }

        $zone->status = v($request->status) ?? true;
        $zone->name = v($request->name);
        $zone->type_id = v($request->type_id);
        $zone->location = v($request->location);
        $zone->geofence = v($request->geofence);
        $zone->save();

        session()->flash('success', $message);
        return response()->json(['result' => $zone]);
    }

    public function delete($id) {
        $zone = Zone::find($id);
        if ($zone) {
            $zone->delete();
        }
        return back()->with('success', 'Zone deleted successfully');
    }

    public function hotels(Request $request, $id = null) {
        $data['zone'] = $id ? Zone::with('hotels')->findOrFail($id) : null;
        $data['hotels'] = Hotel::with('zones')->get();

        return view('zones.hotels', $data);
    }

    public function hotel(Request $request, $id) {
        $zone = Zone::findOrFail($id);

        $request->validate([
            'hotel_id' => 'required',
            'action'   => 'required|in:attach,detach',
        ]);

        if ($request->action === 'attach') {
            $zone->hotels()->syncWithoutDetaching([$request->hotel_id]);
            return response()->json(['message' => 'Hotel attached successfully']);
        } elseif ($request->action === 'detach') {
            $zone->hotels()->detach($request->hotel_id);
            return response()->json(['message' => 'Hotel detached successfully']);
        }

        return response()->json(['message' => 'Invalid action'], 400);
    }
}
