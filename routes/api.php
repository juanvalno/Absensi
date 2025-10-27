<?php

use App\Http\Controllers\Admin\AbsensiController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API untuk absensi realtime
Route::prefix('absensi')->group(function () {
    Route::get('/fetch-latest', [AbsensiController::class, 'fetchLatestData']);
    Route::get('/today', [AbsensiController::class, 'getTodayAttendance']);
    Route::get('/summary', [AbsensiController::class, 'getTodaySummary']);
});