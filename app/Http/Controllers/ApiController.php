<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Geofence;
use App\Models\Task;
use App\Models\Event;

class ApiController extends Controller {
    public function index() {
        return response()->json([
            'devices'   => Device::all(),
            'geofences' => Geofence::all(),
            'tasks'     => Task::all(),
            'events'    => Event::all(),
        ]);
    }
}
