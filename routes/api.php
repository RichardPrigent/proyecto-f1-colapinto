<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RaceScheduleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*// Ruta para obtener el calendario de carreras
Route::get('/race-schedule', [RaceScheduleController::class, 'index'])->middleware('auth:sanctum');*/

// Ruta para obtener el calendario de carreras sin autenticación
Route::get('/race-schedule', [RaceScheduleController::class, 'index']);
