<?php

use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TypeController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\PushController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ZoneController;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureOtpVerified;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    if (Auth::check()) return redirect()->route('dashboard');
    return view('auth.otp-login');
});

// OTP routes (only require auth)
Route::middleware(['auth'])->prefix('otp')->group(function () {
    Route::get('/verify', [OtpController::class, 'showVerifyForm'])->name('otp.verify');
    Route::post('/verify', [OtpController::class, 'verify'])->name('otp.verify.submit')->middleware('throttle:10,10');
    Route::post('/resend', [OtpController::class, 'resend'])->name('otp.resend')->middleware('throttle:10,10');
    Route::get('/remaining', [OtpController::class, 'remaining'])->name('otp.remaining');
});

// Protected routes (require auth + OTP verification)
// Route::middleware(['auth' /*, 'otp.verified'*/ ])->group(function () {
Route::middleware(['auth', EnsureOtpVerified::class])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/push', [PushController::class, 'handle'])->name('push');

    Route::post('/profile/select-hotel', [ProfileController::class, 'selectHotel'])->name('profile.selectHotel');
    Route::post('/update-location', [ProfileController::class, 'updateLocation'])->name('profile.updateLocation');
    Route::get('/sidebar/counts', [SidebarController::class, 'counts'])->name('sidebar.counts');

    Route::middleware([CheckPermission::class . ':tasks'])->prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
        Route::get('/live', [TaskController::class, 'live'])->name('tasks.live');
        Route::post('/upsert', [TaskController::class, 'upsert'])->name('tasks.upsert');
        Route::delete('/delete/{id}', [TaskController::class, 'delete'])->name('tasks.delete');
        Route::post('/duplicate/{id}', [TaskController::class, 'duplicate'])->name('tasks.duplicate');
    });

    Route::middleware([CheckPermission::class . ':events'])->prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('events.index');
        Route::get('/live', [EventController::class, 'live'])->name('events.live');
    });

    Route::middleware([CheckPermission::class . ':buses'])->prefix('buses')->group(function () {
        Route::get('/', [BusController::class, 'index'])->name('buses.index');
        Route::post('/upsert', [BusController::class, 'upsert'])->name('buses.upsert');

        Route::get('/live', [BusController::class, 'live'])->name('buses.live');
        Route::get('/qrcodes', [BusController::class, 'bulkQRCodes'])->name('buses.qrcodes');
        Route::get('/{bus}/qrcode', [BusController::class, 'singleQRCode'])->name('buse.qrcode');
        Route::get('/list', [BusController::class, 'listBuses'])->name('buses.list');

        Route::post('/scan', [BusController::class, 'scan'])->name('buses.scan');
        Route::post('/decrypt-qr', [BusController::class, 'decryptQr'])->name('buses.decryptQr');
    });

    Route::middleware([CheckPermission::class . ':guests'])->prefix('guests')->group(function () {
        Route::get('/', [GuestController::class, 'index'])->name('guests.index');
        Route::get('/live', [GuestController::class, 'live'])->name('guests.live');
    });

    Route::middleware([CheckPermission::class . ':reservations'])->prefix('reservations')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/live', [ReservationController::class, 'live'])->name('reservations.live');
        Route::post('/reserve', [ReservationController::class, 'reserve'])->name('reservations.reserve');

        Route::post('/{reservation}/approve', [ReservationController::class, 'approve'])->name('reservations.approve')
            ->middleware([CheckPermission::class . ':reservations.approve']);
        Route::post('/{reservation}/reject', [ReservationController::class, 'reject'])->name('reservations.reject')
            ->middleware([CheckPermission::class . ':reservations.reject']);

        Route::post('/import', [ReservationController::class, 'import'])->name('reservations.import');
        Route::get('/users', [ReservationController::class, 'users'])->name('reservations.users');
    });

    Route::middleware([CheckPermission::class . ':settings.staff'])->prefix('staff')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('staff.index');
        Route::post('/upsert', [StaffController::class, 'upsert'])->name('staff.upsert');
        Route::delete('/{user}', [StaffController::class, 'destroy'])->name('staff.destroy');
    });

    Route::middleware([CheckPermission::class . ':settings.types'])->prefix('types')->group(function () {
        Route::get('/{id?}', [TypeController::class, 'index'])->name('types.index');
        Route::post('/upsert', [TypeController::class, 'upsert'])->name('types.upsert');
        Route::delete('/delete/{id}', [TypeController::class, 'delete'])->name('types.delete');
    });

    Route::middleware([CheckPermission::class . ':settings.zones'])->prefix('zones')->group(function () {
        Route::get('/', [ZoneController::class, 'index'])->name('zones.index');
        Route::post('/upsert', [ZoneController::class, 'upsert'])->name('zones.upsert');
        Route::delete('/delete/{id}', [ZoneController::class, 'delete'])->name('zones.delete');

        Route::get('/{id}/hotels', [ZoneController::class, 'hotels'])->name('zones.hotels');
        Route::post('/{id}/hotel', [ZoneController::class, 'hotel'])->name('zones.hotel');
    });

    Route::middleware([CheckPermission::class . ':sites'])->prefix('sites')->group(function () {
        Route::get('/', [SiteController::class, 'index'])->name('sites.index');
        Route::post('/upsert', [SiteController::class, 'upsert'])->name('sites.upsert');
        Route::delete('/delete/{id}', [SiteController::class, 'delete'])->name('sites.delete');
    });

    Route::middleware([CheckPermission::class . ':complaints'])->prefix('complaints')->group(function () {
        Route::get('/{id?}', [ComplaintController::class, 'index'])->name('complaints.index');
        Route::post('/upsert', [ComplaintController::class, 'upsert'])->name('complaints.upsert');
        Route::delete('/delete/{id}', [ComplaintController::class, 'delete'])->name('complaints.delete');
    });

    Route::middleware([CheckPermission::class . ':scans'])->prefix('scans')->group(function () {
        Route::get('/', [ScanController::class, 'index'])->name('scans.index');
        Route::get('/live', [ScanController::class, 'index'])->name('scans.live');
        Route::post('/store', [ScanController::class, 'store'])->name('scans.store');
        Route::post('/preview', [ScanController::class, 'preview'])->name('scans.preview');

        Route::get('/indexDEV', [ScanController::class, 'indexDEV'])->name('scans.indexDEV');
        Route::get('/liveDEV', [ScanController::class, 'indexDEV'])->name('scans.liveDEV');
    });

    Route::middleware([CheckPermission::class . ':settings.companies'])->prefix('companies')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('companies.index');
        Route::get('/live', [CompanyController::class, 'live'])->name('companies.live');
        Route::post('/upsert', [ComplaintController::class, 'upsert'])->name('companies.upsert');
    });


    Route::middleware([CheckPermission::class . ':statistics'])->prefix('statistics')->group(function () {
        Route::get('bus-score', [StatisticController::class, 'busScore'])->name('statistics.bus-score');
        Route::get('operator-score', [StatisticController::class, 'operatorScore'])->name('statistics.operator-score');
        Route::get('supervisor-score', [StatisticController::class, 'supervisorScore'])->name('statistics.supervisor-score');
        Route::get('company-score', [StatisticController::class, 'companyScore'])->name('statistics.company-score');
    });

    Route::prefix('reports')->group(function () {
        Route::get('/operator-bus/{operator?}', [ReportController::class, 'operatorBus'])->name('reports.operator-bus');
        Route::get('/supervisor-zone/{supervisor?}', [ReportController::class, 'supervisorZone'])->name('reports.supervisor-zone');
    });

    
    Route::get('/dump-autoload', function () {
        Artisan::call('optimize:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        return 'Autoload refreshed';
    });
    
});


Route::prefix('cron')->group(function () {
    Route::get('/run-queue', [CronController::class, 'runQueue']);
});

require __DIR__ . '/auth.php';
