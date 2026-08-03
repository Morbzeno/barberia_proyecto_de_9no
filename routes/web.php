<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

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

// Citas
Route::get('/citas/{chairID}/{date}', [AppointmentController::class, 'showDay'])
    ->name('appointments.index');

// Productos
Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');

// Carrito
Route::get('/add-to-cart/{product}', [CartController::class, 'add'])
    ->name('cart.add');

Route::get('/cart/{id}', [CartController::class, 'show'])
    ->name('cart.show');

// Panel de administración
require __DIR__.'/admin.php';