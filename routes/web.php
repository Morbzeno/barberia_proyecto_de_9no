<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\GoogleAuthController;

// Landing Page Principal
Route::get('/', function () {
    return view('home');
})->name('home');

// Inicio de sesión
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Registro
Route::get('/registro', function () {
    return view('auth.register');
})->name('register');

Route::post('/registro', [ClientController::class, 'store'])
    ->name('register.store');

// Citas
Route::get('/citas/{chairID}/{date}', [AppointmentController::class, 'showDay'])
    ->name('appointments.index');

// Productos
Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');

// Carrito
Route::get('/cart/{id}', [CartController::class, 'show'])->name('cart.show');

Route::get('/add-to-cart/{product}', [CartController::class, 'add'])
    ->name('cart.add');

// Google Auth
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Panel de administración
require __DIR__.'/admin.php';
