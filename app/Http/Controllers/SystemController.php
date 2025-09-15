<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SystemController extends Controller {
    /**
     * Check if the request is authorized via SYSTEM_KEY.
     */
    private function authorizeRequest(Request $request): void {
        if ($request->key !== env('SYSTEM_KEY')) {
            abort(403, 'Unauthorized');
        }
    }

    // curl -s "https://bus.iatf-dz.com/system/fetch-all?key=3f9D8a7B-c2E1-4F6b-91e2-7dA5f8C0b1E2" > /dev/null 2>&1
    /**
     * Run the fetch-all process (fetch data from external sources).
     */
    public function fetchAll(Request $request) {
        $this->authorizeRequest($request);

        $busCount   = (new BusController())->fetch();
        $hotelCount = (new HotelController())->fetch();
        $taskCount  = (new TaskController())->fetch();
        $eventCount = (new EventController())->fetch();

        return response()->json([
            'status'  => 'ok',
            'buses'   => $busCount,
            'hotels'  => $hotelCount,
            'tasks'   => $taskCount,
            'events'  => $eventCount,
            'time'    => now()->toDateTimeString(),
        ]);
    }

    // curl -s "https://bus.iatf-dz.com/system/run-queue?key=3f9D8a7B-c2E1-4F6b-91e2-7dA5f8C0b1E2" > /dev/null 2>&1
    /**
     * Run the queue worker once (process pending jobs).
     */
    public function runQueue(Request $request) {
        $this->authorizeRequest($request);

        Artisan::call('queue:work --stop-when-empty --tries=3');

        return response()->json([
            'status'  => 'ok',
            'message' => 'Queue processed',
            'time'    => now()->toDateTimeString(),
        ]);
    }

}
