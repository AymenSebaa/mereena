<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\Complaint;
use App\Models\Event;
use App\Models\Site;
use App\Models\Task;

class DashboardController extends Controller {
    public function index(Request $request) {
        $user = $request->user();
        $profile = $user->profile;

        // dd($user);

        // Initialize counts
        $busesCount = Bus::count();
        $sitesCount = Site::count();
        $complaintsCount = Complaint::count();
        $eventsCount = Event::count();
        $tasksCount = Task::count();
        $locations = [];

        // Recent tasks
        $recentTasks = TaskController::getUserQuery($user)
            ->limit(5)
            ->get([
                'id',
                'title',
                'site_id',
                'pickup_address',
                'pickup_address_lat',
                'pickup_address_lng',
                'pickup_time_from',
                'pickup_time_to'
            ]);

        // Filter counts based on role
        $task_count = TaskController::getUserQuery($user)->count();
        $event_count = EventController::getUserQuery($user)->count();
        $complaint_count = ComplaintController::getUserQuery($user)->count();
        $bus_count = BusController::getUserQuery($user)->count();
        $site_count = SiteController::getUserQuery($user)->count();
        $guest_count = GuestController::getUserQuery($user)->count();
        
        // QR payload
        $payload = [
            'name' => $user->name,
            'type' => 'users',
            'type_id' => $user->id,
            'action' => 'checkin',
        ];
        $qrcode = ScanController::encrypt128(json_encode($payload));

        return view('dashboard', compact(
            'task_count',
            'event_count',
            'complaint_count',
            'bus_count',
            'site_count',
            'locations',
            'recentTasks',
            'qrcode'
        ));
    }
}
