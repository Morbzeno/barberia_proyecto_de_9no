<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;

// Ruta de la Landing Page Principal con el calendario de reservas
Route::get('/', function () {
    return view('home');
})->name('home');

// Ruta para la pantalla de inicio de sesión
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Ruta para la pantalla de registro de cuentas
Route::get('/registro', function () {
    return view('auth.register');
})->name('register');

Route::get('/citas/{chairID}/{date}', [AppointmentController::class, 'showDay'])->name('appointments.index');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::get('/add-to-cart/{product}', [CartController::class, 'add'])->name('cart.add');

Route::get('/cart/{id}', [CartController::class, 'show'])->name('cart.show');