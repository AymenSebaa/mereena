<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SidebarController extends Controller {

    public function counts(Request $request) {
        $user = $request->user();

        $task_count = TaskController::getUserQuery($user)->count();
        $event_count = EventController::getUserQuery($user)->count();
        $complaint_count = ComplaintController::getUserQuery($user)->count();
        $bus_count = BusController::getUserQuery($user)->count();
        $hotel_count = HotelController::getUserQuery($user)->count();
        $guest_count = GuestController::getUserQuery($user)->count();
        $scan_count = ScanController::getUserQuery($user)->count();
        $reservation_count = ReservationController::getUserQuery($user)->count();

        return response()->json([
            'task_count'  => $task_count,
            'event_count'  => $event_count,
            'complaint_count'  => $complaint_count,
            'bus_count'  => $bus_count,
            'hotel_count'  => $hotel_count,
            'guest_count'  => $guest_count,
            'scan_count'  => $scan_count,
            'reservation_count'  => $reservation_count,
        ]);
    }
}
