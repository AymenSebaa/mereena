<?php

namespace App\Http\Controllers;

use App\Mail\ScanMail;
use App\Mail\TaskMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Task;
use App\Models\Bus;
use App\Models\Site;
use App\Models\SiteZone;
use App\Models\User;
use App\Models\Zone;
use App\Services\GoogleMapsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller {
    public function index(Request $request) {
        // initial load → tasks will be fetched via live()
        $tasks = [];
        return view('tasks.index', compact('tasks'));
    }

    public function live(Request $request) {
        $query = $this->getUserQuery($request->user());

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('pickup_address', 'like', "%{$search}%")
                    ->orWhere('delivery_address', 'like', "%{$search}%");
            });
        }

        $tasks = $query->with('site')->latest()->get();
        $buses = Bus::all()->keyBy('external_id');

        return response()->json([
            'tasks' => $tasks,
            'buses' => $buses
        ]);
    }

    public function upsert(Request $request) {
        $request->validate([
            'id' => 'nullable|exists:tasks,id',
            'title' => 'required|string|max:255',
            'comment' => 'nullable|string',
            'priority' => 'nullable|numeric',
            'status' => 'nullable|numeric',
            'invoice_number' => 'nullable|string',
            'pickup_address' => 'required|string',
            'pickup_address_lat' => 'required|numeric',
            'pickup_address_lng' => 'required|numeric',
            'delivery_address' => 'required|string',
            'delivery_address_lat' => 'required|numeric',
            'delivery_address_lng' => 'required|numeric',
        ]);

        if ($request->id) {
            // UPDATE
            $task = Task::findOrFail($request->id);
            $oldPickupTime = Carbon::parse($task->pickup_time_from);

            $task->update($request->all());
            $newPickupTime = Carbon::parse($task->pickup_time_from);

            // only notify if pickup_time_from changed
            if (!$oldPickupTime->equalTo($newPickupTime)) {
                $this->notifyUsers($task);
            }
        } else {
            // CREATE
            if (true || !$request->device_id) {
                $task = Task::create($request->all());
            } else {
                $response = Http::post(env('DJAZFLEET_API') . '/tasks', $request->all());

                if (!$response->successful()) {
                    return $request->ajax()
                        ? response()->json(['status' => 0, 'message' => 'Failed to sync with DjazFleet'])
                        : back()->withErrors(['error' => 'Failed to sync with DjazFleet']);
                }

                $data = $response->json();
                $task = Task::create(array_merge($request->all(), [
                    'external_id' => $data['id'] ?? null,
                ]));
            }

            // always notify on new tasks
            $this->notifyUsers($task);
        }

        $site = Site::all()->sortBy(function ($site) use ($task) {
            return distance(
                $site->lat,
                $site->lng,
                $task->pickup_address_lat,
                $task->pickup_address_lng
            );
        })->first();
        $task->site_id = $site->id;

        // Fetch and save Google Directions
        $source = ['lat' => $task->pickup_address_lat, 'lng' => $task->pickup_address_lng];
        $destination = ['lat' => $task->delivery_address_lat, 'lng' => $task->delivery_address_lng];
        $directions = GoogleMapsService::getDirections($source, $destination);

        if ($directions) {
            $task->distance   = $directions['distance'];
            $task->duration   = $directions['duration'];
            $task->polyline   = $directions['polyline'];
            $task->directions = $directions['steps'];
        }

        $task->save();

        return response()->json([
            'status' => 1,
            'task'   => $task->load(['site', 'bus']),
            'message' => 'Task saved successfully.'
        ]);
    }

    protected function notifyUsers($task) {
        if (!$task->status || !$task->site_id || !$task->pickup_time_from) return;

        $site_id = $task->site_id;
        $delay = Carbon::parse($task->pickup_time_from)->subMinutes(15);

        // Admin (1) & Manager (2) → get all
        $adminsManagers = User::whereHas('profile', fn($q) => $q->whereIn('role_id', [1, 2]))->get();
        foreach ($adminsManagers as $user) {
            Mail::to($user->email)->later($delay, new TaskMail($task, $user->profile->role_id));
        }

        // Supervisors (3) & Dispatchers (6) → by zone
        $zoneIds = [];

        if ($site_id) {
            $zoneIds = SiteZone::where('site_id', $site_id)->pluck('zone_id');
        }

        if ($zoneIds->isNotEmpty()) {
            $supDispatch = User::whereHas('profile', function ($q) use ($zoneIds) {
                $q->whereIn('role_id', [3, 6])->whereIn('zone_id', $zoneIds);
            })->get();

            foreach ($supDispatch as $user) {
                Mail::to($user->email)->later($delay, new TaskMail($task, $user->profile->role_id));
            }
        }

        // Guests (10) → same site as operator
        if ($site_id) {
            $guests = User::whereHas('profile', function ($q) use ($site_id) {
                $q->where('role_id', 10)
                    ->where('site_id', $site_id);
            })->get();

            foreach ($guests as $user) {
                Mail::to($user->email)->later($delay, new TaskMail($task, $user->profile->role_id));
            }
        }
    }

    public function duplicate($id) {
        $task = Task::findOrFail($id);

        $newTask = $task->replicate();
        $newTask->status = 0; // disabled by default
        $newTask->save();

        return response()->json([
            'status' => 1,
            'task'   => $newTask->load(['site', 'bus']),
            'message' => 'Task duplicated successfully.'
        ]);
    }

    public function delete(Request $request, $id) {
        $task = Task::findOrFail($id);

        $task->delete();

        if ($request->ajax()) {
            return response()->json([
                'status' => 1,
                'message' => 'Task deleted successfully.'
            ]);
        }

        return back()->with('success', 'Task deleted successfully.');
    }


    public function fetch() {
        $url = env('DJAZ_BASE_URL') . '/get_tasks?user_api_hash=' . env('DJAZFLEET_API_HASH');
        $proxyUrl = "https://odiro-dz.com/tfa/public/url";
        $response = Http::get($proxyUrl, ['url' => $url]);

        if ($response->ok()) {
            $tasks = $response->json('items.data', []);
            $count = 0;

            foreach ($tasks as $t) {
                // Find nearest site to pickup location
                $site = Site::all()->sortBy(function ($site) use ($t) {
                    return distance(
                        $site->lat,
                        $site->lng,
                        $t['pickup_address_lat'],
                        $t['pickup_address_lng']
                    );
                })->first();

                Task::updateOrCreate(
                    ['external_id' => v($t['id'])],
                    [
                        'site_id'             => v($site?->id),
                        'device_id'            => v($t['device_id']),
                        'user_id'              => v($t['user_id']),
                        'title'                => v($t['title']),
                        'comment'              => v($t['comment']),
                        'priority'             => v($t['priority']),
                        'status'               => v($t['status']),
                        'invoice_number'       => v($t['invoice_number']),
                        'pickup_address'       => v($t['pickup_address']),
                        'pickup_address_lat'   => v($t['pickup_address_lat']),
                        'pickup_address_lng'   => v($t['pickup_address_lng']),
                        'pickup_time_from'     => v($t['pickup_time_from']),
                        'pickup_time_to'       => v($t['pickup_time_to']),
                        'delivery_address'     => v($t['delivery_address']),
                        'delivery_address_lat' => v($t['delivery_address_lat']),
                        'delivery_address_lng' => v($t['delivery_address_lng']),
                        'delivery_time_from'   => v($t['delivery_time_from']),
                        'delivery_time_to'     => v($t['delivery_time_to']),
                    ]
                );
                $count++;
            }

            return "Tasks fetched and saved: $count";
        }

        return "Failed to fetch tasks: " . $response->status();
    }

    public static function getUserQuery($user) {
        $query = Task::with(['site', 'bus']); // eager load site and bus
        $profile = $user->profile ?? null;

        if ($profile && in_array($profile->role_id, [3, 4, 6, 10])) {
            if ($profile->site_id) {
                $query->where('site_id', $profile->site_id);
            } else if ($profile->zone_id) {
                $zone = Zone::find($profile->zone_id);
                $siteIds = $zone?->sites->pluck('id') ?? collect();
                $query->whereIn('site_id', $siteIds);
            } else {
                $query->whereRaw('0=1');
            }
        }

        return $query;
    }
}
