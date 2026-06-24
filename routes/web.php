<?php

use Illuminate\Support\Facades\Route;

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