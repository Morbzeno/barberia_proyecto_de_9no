@extends('layouts.app')

@section('title', 'Registro | Machin Barber')

@section('styles')
<style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body{
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background-image: url('https://i.pinimg.com/736x/12/66/6a/12666ad1bb6e0a2b5820ddd2b10f8bae.jpg');
        background-size: cover;
        background-position: center;
        font-family: 'Poppins', sans-serif;
        overflow: hidden;
    }

    body::before{
        content: "";
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.55);
    }

    .register-box{
        position: relative;
        width: 550px;
        padding: 50px 45px;
        background: rgba(255,255,255,0.08);
        border-radius: 25px;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.15);
        box-shadow: 0 8px 35px rgba(0,0,0,0.45);
        color: white;
    }

    .logo{
        text-align: center;
        font-family: 'Cinzel', serif;
        font-size: 42px;
        font-weight: 700;
        color: #d4af37;
        letter-spacing: 3px;
        margin-bottom: 10px;
    }

    .subtitle{
        text-align: center;
        margin-bottom: 35px;
        color: rgba(255,255,255,0.75);
        letter-spacing: 1px;
    }

    .input-box{
        margin-bottom: 22px;
    }

    .input-box label{
        display: block;
        margin-bottom: 8px;
        font-size: 15px;
        color: #f5f5f5;
    }

    .input-box input{
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 12px;
        background: rgba(255,255,255,0.12);
        color: white;
        font-size: 15px;
        outline: none;
    }

    .input-box input::placeholder{
        color: rgba(255,255,255,0.6);
    }

    .btn-register{
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 12px;
        background: #c9a227;
        color: white;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-register:hover{
        background: #a7861f;
        transform: scale(1.02);
    }

    .login-link{
        text-align: center;
        margin-top: 25px;
        font-size: 14px;
        color: rgba(255,255,255,0.8);
    }

    .login-link a{
        color: #d4af37;
        text-decoration: none;
        font-weight: 500;
    }

    .login-link a:hover{
        text-decoration: underline;
    }
</style>
@endsection

@section('content')
<div class="register-box">
    <div class="logo">Machin Barber</div>
    <div class="subtitle">Crear Cuenta</div>

    <form action="#" method="POST">
        @csrf {{-- Token de seguridad indispensable en Laravel --}}
        
        <div class="input-box">
            <label>Nombre</label>
            <input type="text" name="name" placeholder="Ingresa tu nombre" required>
        </div>

        <div class="input-box">
            <label>Apellido</label>
            <input type="text" name="lastname" placeholder="Ingresa tu apellido" required>
        </div>

        <div class="input-box">
            <label>Correo Electrónico</label>
            <input type="email" name="email" placeholder="Ingresa tu correo" required>
        </div>

        <div class="input-box">
            <label>Número de Teléfono</label>
            <input type="tel" name="phone" placeholder="Ingresa tu número" required>
        </div>

        <button type="submit" class="btn-register">Crear Cuenta</button>
    </form>

    <div class="login-link">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}">Iniciar sesión</a>
    </div>
</div>
@endsection