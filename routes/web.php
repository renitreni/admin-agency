<?php

use App\Http\Controllers\ComplaintController;
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
