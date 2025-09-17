<?php

namespace App\Http\Controllers;

use App\Services\ModuleManager;
use Illuminate\Http\Request;

class SidebarController extends Controller {

    public function counts(Request $request) {
        $user = $request->user();

        $data['task_count'] = TaskController::getUserQuery($user)->count();
        $data['event_count'] = EventController::getUserQuery($user)->count();
        $data['complaint_count'] = ComplaintController::getUserQuery($user)->count();
        $data['bus_count'] = BusController::getUserQuery($user)->count();
        $data['hotel_count'] = HotelController::getUserQuery($user)->count();
        $data['guest_count'] = GuestController::getUserQuery($user)->count();
        $data['scan_count'] = ScanController::getUserQuery($user)->count();
        $data['reservation_count'] = ReservationController::getUserQuery($user)->count();

        $data['module'] = ModuleManager::all();

        return response()->json($data);
    }
}
