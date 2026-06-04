<?php

use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\MonitoringController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect(route('filament.admin.auth.login'));
})->name('home');

// Serve storage files (fallback when symlink fails, e.g. in Docker with volume mount)
Route::get('/storage/{path}', function (string $path) {
    $path = str_replace('..', '', $path);
    if (! Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return response()->file(Storage::disk('public')->path($path));
})->where('path', '.*')->name('storage.serve');

Route::get('/complaints', [ComplaintController::class, 'show'])->name('complaints.form');
Route::post('/complaints', [ComplaintController::class, 'store'])->middleware('throttle:complaints')->name('complaints.store');

Route::middleware('monitoring.locale')->group(function () {
    Route::get('/monitoring/login', [MonitoringController::class, 'showLogin'])->name('monitoring.login.show');
    Route::post('/monitoring/login', [MonitoringController::class, 'authenticate'])->middleware('throttle:20,1')->name('monitoring.login.authenticate');
    Route::post('/monitoring/logout', [MonitoringController::class, 'logout'])->name('monitoring.logout');
    Route::post('/monitoring/locale', [MonitoringController::class, 'switchLocale'])->name('monitoring.locale.switch');

    Route::get('/monitoring/reporting', [MonitoringController::class, 'showForm'])->name('monitoring.form.show');
    Route::post('/monitoring/reporting', [MonitoringController::class, 'store'])->middleware('throttle:20,1')->name('monitoring.form.store');
    Route::post('/monitoring/emergency', [MonitoringController::class, 'storeEmergency'])->middleware('throttle:5,1')->name('monitoring.emergency.store');
});
