<?php

use App\Http\Controllers\Api\AttendeeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::apiResource('/events', EventController::class);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/events', EventController::class)->except(['index', 'show']);
    
    Route::apiResource('/events.attendees', AttendeeController::class)
        ->scoped()
        ->except(['index', 'show', 'update'])
        ->only(['store', 'destroy'])
        ->middleware('throttle:attendees');
});