<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('', [AuthController::class, 'login']);
});

Route::prefix('trips')->middleware('auth:sanctum')->group(function () {
    Route::get('', [TripController::class, 'index']);
    Route::post('', [TripController::class, 'store']);
    Route::get('/{uuid}', [TripController::class, 'show']);
    Route::put('/{uuid}', [TripController::class, 'update']);
});
