<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\GeofenceController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\ApiController;

Route::get('/djazfleet', [ApiController::class, 'index']);

Route::prefix('djazfleet')->group(function () {
    Route::get('devices',   [DeviceController::class, 'index']);
    Route::get('geofences', [GeofenceController::class, 'index']);
    Route::get('tasks',     [TaskController::class, 'index']);
    Route::get('events',    [EventController::class, 'index']);
});
