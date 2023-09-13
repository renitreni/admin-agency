<?php

use App\Http\Controllers\API\ApplicationController;
use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('api')->group(function () {
    Route::apiResource('application', ApplicationController::class)->only('index');
    Route::post('/application/{agency}', [ApplicationController::class, 'store']);
    Route::post('/application/{agency}/job/{jobPost}', [ApplicationController::class, 'storeWithJob'])->name('application.store.job');

    Route::apiResource('job-post/{agency}', JobController::class)->only('index');
    Route::get('/job-post/get/{jobPost}', [JobController::class, 'show']);
});
