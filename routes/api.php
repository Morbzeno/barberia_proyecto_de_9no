<?php

use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------
// AUTENTICACIÓN
// ---------------------------------------------------------

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;

// ---------------------------------------------------------
// CONTROLADORES
// ---------------------------------------------------------

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChairController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\ServiceController;

// ---------------------------------------------------------
// AUTENTICACIÓN PÚBLICA
// ---------------------------------------------------------

Route::middleware('guest')->group(function () {

    Route::post(
        '/register',
        [RegisteredUserController::class, 'store']
    )->name('register');

    Route::post(
        '/login',
        [AuthenticatedSessionController::class, 'store']
    )->name('login');

    Route::post(
        '/forgot-password',
        [PasswordResetLinkController::class, 'store']
    )->name('password.email');

    Route::post(
        '/reset-password',
        [NewPasswordController::class, 'store']
    )->name('password.store');
});

// ---------------------------------------------------------
// INFORMACIÓN PÚBLICA
// ---------------------------------------------------------

Route::get(
    '/services',
    [ServiceController::class, 'index']
);

Route::get(
    '/services/{id}',
    [ServiceController::class, 'show']
);

Route::get(
    '/products',
    [ProductController::class, 'index']
);

Route::get(
    '/products/{id}',
    [ProductController::class, 'show']
);

Route::get(
    '/categories',
    [CategoryController::class, 'index']
);

Route::get(
    '/categories/{id}',
    [CategoryController::class, 'show']
);

Route::get(
    '/chairs',
    [ChairController::class, 'index']
);

Route::get(
    '/chairs/{id}',
    [ChairController::class, 'show']
);

Route::get(
    '/appointmentsEmployeeDay/{date}/{employeeID}',
    [AppointmentController::class, 'showDailyAppointmentsByEmployee']
);

// ---------------------------------------------------------
// PAYPAL
// Estas deben ser públicas porque PayPal redirige aquí.
// ---------------------------------------------------------

Route::get(
    '/paypal/return',
    [CartController::class, 'paypalReturn']
)->name('paypal.return');

Route::get(
    '/paypal/cancel',
    [CartController::class, 'paypalCancel']
);

// ---------------------------------------------------------
// RUTAS CON AUTENTICACIÓN SANCTUM
// ---------------------------------------------------------

Route::middleware('auth:sanctum')->group(function () {

    // -----------------------------------------------------
    // PERFIL
    // -----------------------------------------------------

    Route::get(
        '/profile',
        [ClientController::class, 'profile']
    );

    Route::put(
        '/profile',
        [ClientController::class, 'updateProfile']
    );

    // Lista de barberos para Android
    Route::get(
        '/barbers',
        [EmployeeController::class, 'barbers']
    );

    Route::get(
    '/barber/appointments',
    [AppointmentController::class, 'barberAppointments']
);

Route::get(
    '/client-appointments',
    [AppointmentController::class, 'showClient']
);

Route::put(
    '/profile',
    [ClientController::class, 'updateMyProfile']
);
    // -----------------------------------------------------
    // LOGOUT
    // -----------------------------------------------------

    Route::post(
        '/logout',
        [AuthenticatedSessionController::class, 'destroy']
    )->name('logout');

    // -----------------------------------------------------
    // VERIFICACIÓN EMAIL
    // -----------------------------------------------------

    Route::get(
        '/verify-email/{id}/{hash}',
        VerifyEmailController::class
    )
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post(
        '/email/verification-notification',
        [EmailVerificationNotificationController::class, 'store']
    )
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // =====================================================
    // CLIENTE
    // =====================================================

    Route::middleware('client')->group(function () {

        // -------------------------------------------------
        // CITAS DEL CLIENTE
        // -------------------------------------------------

        Route::get(
            '/showClient',
            [AppointmentController::class, 'showClient']
        );

        Route::post(
            '/appointments',
            [AppointmentController::class, 'store']
        );

        Route::get(
            '/clientDailyAvailability/{date}/{employeeID?}',
            [AppointmentController::class, 'showDailyAppointments']
        );

        Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-orders', [SellController::class, 'myOrders']);
});

        // -------------------------------------------------
        // CARRITO
        // -------------------------------------------------

        Route::get(
            '/cart/{client}',
            [CartController::class, 'show']
        );

        Route::post(
            '/addCart/{product}/{client}',
            [CartController::class, 'add']
        );

        Route::put(
            '/moreCart/{product}/{client}',
            [CartController::class, 'more']
        );

        Route::put(
            '/lessCart/{product}/{client}',
            [CartController::class, 'less']
        );

        Route::delete(
            '/quitCart/{product}/{client}',
            [CartController::class, 'quitItem']
        );

        // -------------------------------------------------
        // PAYPAL
        // -------------------------------------------------

        Route::post(
            '/paypal/create',
            [CartController::class, 'createPaypalOrder']
        );

        // -------------------------------------------------
        // VENTAS
        // -------------------------------------------------

        Route::post(
            '/sells/{clientID}',
            [SellController::class, 'store']
        );
    });

    // =====================================================
    // BARBERO / RECEPCIONISTA / ADMIN
    // =====================================================

    Route::middleware(
        'worker:barber,receptionist,admin'
    )->group(function () {

        Route::get(
            '/appointments',
            [AppointmentController::class, 'index']
        );

        Route::get(
            '/appointments/{id}',
            [AppointmentController::class, 'show']
        );

        Route::get(
            '/appointmentsDay/{id}/{day}',
            [AppointmentController::class, 'showDay']
        );

        Route::put(
            '/appointments/{id}',
            [AppointmentController::class, 'update']
        );
    });

    // =====================================================
    // RECEPCIONISTA / ADMIN
    // =====================================================

    Route::middleware(
        'worker:receptionist,admin'
    )->group(function () {

        Route::delete(
            '/appointments/{id}',
            [AppointmentController::class, 'destroy']
        );

        Route::put(
            '/appointments/{id}/status/{newStatus}',
            [AppointmentController::class, 'AlterAppointmentStatus']
        );

        Route::get(
            '/clients',
            [ClientController::class, 'index']
        );

        Route::get(
            '/clients/{id}',
            [ClientController::class, 'show']
        );

        Route::post(
            '/clients',
            [ClientController::class, 'store']
        );

        Route::put(
            '/clients/{id}',
            [ClientController::class, 'update']
        );

        Route::get(
            '/sells',
            [SellController::class, 'index']
        );

        Route::post(
            '/sells/{clientID}',
            [SellController::class, 'store']
        );

        Route::get('/orders', [SellController::class, 'orders']);

Route::put(
    '/orders/{id}/status',
    [SellController::class, 'updateOrderStatus']
);
    });

    // =====================================================
    // ADMIN
    // =====================================================

    Route::middleware('worker:admin')->group(function () {

        Route::apiResource(
            'employees',
            EmployeeController::class
        );

        Route::delete(
            '/clients/{id}',
            [ClientController::class, 'destroy']
        );

        // Servicios
        Route::post(
            '/services',
            [ServiceController::class, 'store']
        );

        Route::put(
            '/services/{id}',
            [ServiceController::class, 'update']
        );

        Route::delete(
            '/services/{id}',
            [ServiceController::class, 'destroy']
        );

        // Productos
        Route::post(
            '/products',
            [ProductController::class, 'store']
        );

        Route::put(
            '/products/{id}',
            [ProductController::class, 'update']
        );

        Route::delete(
            '/products/{id}',
            [ProductController::class, 'destroy']
        );

        // Categorías
        Route::post(
            '/categories',
            [CategoryController::class, 'store']
        );

        Route::put(
            '/categories/{id}',
            [CategoryController::class, 'update']
        );

        Route::delete(
            '/categories/{id}',
            [CategoryController::class, 'destroy']
        );

        // Sillas
        Route::post(
            '/chairs',
            [ChairController::class, 'store']
        );

        Route::put(
            '/chairs/{id}',
            [ChairController::class, 'update']
        );

        Route::delete(
            '/chairs/{id}',
            [ChairController::class, 'destroy']
        );

        // PDFs
        Route::get(
            '/pdfAppointments/{filter}/{date}',
            [AppointmentController::class, 'invoke']
        );

        Route::get(
            '/pdfSells/{filter}/{date}',
            [SellController::class, 'dashboard_pdf']
        );
    });
});