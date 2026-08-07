<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

// ---------------------------------------------------------
// AUTENTICACIÓN PÚBLICA
// ---------------------------------------------------------
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/register', [RegisteredUserController::class, 'store']);

// ---------------------------------------------------------
// RUTAS PROTEGIDAS (Sanctum)
// ---------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // Perfil
    Route::get('/profile', [ClientController::class, 'profile']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    // Carrito (Ajustado a ApiService.kt)
    Route::post('/addCart/{product}/{client}', [CartController::class, 'add']);
    Route::get('/cart/{client}', [CartController::class, 'show']);
    Route::put('/moreCart/{product}/{client}', [CartController::class, 'more']);
    Route::put('/lessCart/{product}/{client}', [CartController::class, 'less']);
    Route::delete('/quitCart/{product}/{client}', [CartController::class, 'quitItem']);

    // PayPal
    Route::post('/paypal/create', [CartController::class, 'createPaypalOrder']);
});

// PayPal Return (Pública para que PayPal pueda redirigir)
Route::get('/paypal/return', [CartController::class, 'paypalReturn'])->name('paypal.return');
Route::get('/paypal/cancel', [CartController::class, 'paypalCancel']);

// ---------------------------------------------------------
// RUTAS PÚBLICAS ADICIONALES
// ---------------------------------------------------------
Route::get('/products', [ProductController::class, 'index']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/barbers', [EmployeeController::class, 'barbers']);
Route::get('/showClient', [AppointmentController::class, 'showClient']);
Route::put('/appointments/{id}/status/{status}', [AppointmentController::class, 'AlterAppointmentStatus']);
