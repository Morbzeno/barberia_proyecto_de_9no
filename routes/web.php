<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\SellController;
use App\Models\Service;

// Landing Page Principal
Route::get('/', function () {
    $services = Service::all();

    return view('home', compact('services'));
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

    Route::get('/shop/products', [ProductController::class, 'shop'])
    ->name('shop.products');

// Carrito
Route::get('/add-to-cart/{product}', [CartController::class, 'add'])
    ->name('cart.add');
Route::post('/cart', [CartController::class, 'add'])
    ->name('cart.add');
Route::get('/cart/{id}', [CartController::class, 'show'])
    ->name('cart.show');
Route::get('/carrito', function () {
    return view('cart.index');
})->name('cart.view');

// Direcciones
Route::middleware('auth')->group(function () {
    Route::get('/directions', [DirectionController::class, 'index'])
        ->name('directions.index');
    Route::post('/directions', [DirectionController::class, 'store'])
        ->name('directions.store');
});

// PayPal Sandbox
Route::middleware('auth')->group(function () {
    Route::post(
        '/paypal/create-order',
        [PayPalController::class, 'createOrder']
    )->name('paypal.create');
    Route::post(
        '/paypal/capture-order',
        [PayPalController::class, 'captureOrder']
    )->name('paypal.capture');
});

Route::get(
    '/mis-citas',
    [AppointmentController::class, 'myAppointments']
)
->middleware('auth')
->name('appointments.mine');

Route::get(
    '/mis-compras',
    [SellController::class, 'myPurchases']
)
->middleware('auth')
->name('purchases.mine');


Route::get(
    '/mis-compras/{sell}',
    [SellController::class, 'showPurchase']
)
->middleware('auth')
->name('purchases.show');



// Panel de administración
require __DIR__.'/admin.php';