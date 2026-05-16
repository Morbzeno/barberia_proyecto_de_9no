<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AppointmentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('employees', EmployeeController::class);
Route::apiResource('clients', ClientController::class);
Route::apiResource('service', ServiceController::class);
Route::apiResource('appointment', AppointmentController::class);