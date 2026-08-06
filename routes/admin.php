<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ChairController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\SellController;
use App\Http\Controllers\Admin\PaymentController;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categorias', CategoryController::class)
        ->parameters(['categorias' => 'category'])
        ->names('categories');

    Route::resource('productos', ProductController::class)
        ->parameters(['productos' => 'product'])
        ->names('products');

    Route::resource('servicios', ServiceController::class)
        ->parameters(['servicios' => 'service'])
        ->names('services');

    Route::resource('sillas', ChairController::class)
        ->parameters(['sillas' => 'chair'])
        ->names('chairs');

    Route::resource('empleados', EmployeeController::class)
        ->parameters(['empleados' => 'employee'])
        ->names('employees');

    Route::resource('clientes', ClientController::class)
        ->parameters(['clientes' => 'client'])
        ->names('clients');

    Route::resource('citas', AppointmentController::class)
        ->parameters(['citas' => 'appointment'])
        ->names('appointments');

    Route::get('ventas', [SellController::class, 'index'])->name('sells.index');
    Route::get('ventas/{sell}', [SellController::class, 'show'])->name('sells.show');
    Route::delete('ventas/{sell}', [SellController::class, 'destroy'])->name('sells.destroy');

    Route::get('pagos', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('pagos/{payment}', [PaymentController::class, 'show'])->name('payments.show');
});
