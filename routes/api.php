<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Domain Controllers
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ChairController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

// Auth Controllers
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest')->name('api.register');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest')->name('api.login');
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('guest')->name('password.email');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->middleware('guest')->name('password.store');

Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/chairs', [ChairController::class, 'index']);
Route::get('/chairs/{id}', [ChairController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/barbers', [EmployeeController::class, 'barbers']);

    // ---------------- CLIENTE ----------------
    Route::middleware('client')->group(function () {
        Route::get('/showClient', [AppointmentController::class, 'showClient']);
        Route::post('/appointments', [AppointmentController::class, 'store']);
        Route::get('/clientDailyAvailability/{date}/{employeeID?}', [AppointmentController::class, 'showDailyAppointments']);

        Route::get('/cart/{id}', [CartController::class, 'show']);
        Route::post('/addCart/{product}/{client}', [CartController::class, 'add']);
        Route::delete('/quitCart/{product}/{client}', [CartController::class, 'quitItem']);
        Route::put('/moreCart/{product}/{client}', [CartController::class, 'more']);
        Route::put('/lessCart/{product}/{client}', [CartController::class, 'less']);
        Route::post('/paypal/create', [CartController::class, 'createPaypalOrder']);
    });

    // ---------------- TRABAJADORES (Barber, Rec, Admin) ----------------
    Route::middleware('worker:barber,receptionist,admin')->group(function () {
        Route::get('/appointments', [AppointmentController::class, 'index']);
        Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
        Route::get('/appointmentsDay/{id}/{day}', [AppointmentController::class, 'showDay']);
        Route::put('/appointments/{id}', [AppointmentController::class, 'update']);
    });

    // ---------------- RECEPCIONISTA / ADMIN ----------------
    Route::middleware('worker:receptionist,admin')->group(function () {
        Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy']);
        Route::put('/appointments/{id}/status/{newStatus}', [AppointmentController::class, 'AlterAppointmentStatus']);

        Route::apiResource('users', UserController::class);
        Route::apiResource('clients', ClientController::class);
    });

    // ---------------- ADMIN ONLY ----------------
    Route::middleware('worker:admin')->group(function () {
        Route::apiResource('employees', EmployeeController::class);
        Route::post('/services', [ServiceController::class, 'store']);
        Route::put('/services/{id}', [ServiceController::class, 'update']);
        Route::delete('/services/{id}', [ServiceController::class, 'destroy']);

        Route::post('/chairs', [ChairController::class, 'store']);
        Route::put('/chairs/{id}', [ChairController::class, 'update']);
        Route::delete('/chairs/{id}', [ChairController::class, 'destroy']);
    });

    // Specific Product/Category write access
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});
