<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CronController extends Controller {
    /**
     * Check if the request is authorized via CRON_KEY.
     */
    private function authorizeRequest(Request $request): void {
        if ($request->key !== env('CRON_KEY')) {
            abort(403, 'Unauthorized');
        }
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
