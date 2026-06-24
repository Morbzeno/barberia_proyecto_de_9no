@extends('layouts.app')

@section('title', 'Iniciar Sesión | Machin Barber')

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
        background-image: url('https://i.pinimg.com/1200x/f4/9a/5b/f49a5bbcefa352b47d44f7176ba91cc7.jpg');
        background-size: cover;
        background-position: center;
        overflow: hidden;
        font-family: 'Poppins', sans-serif;
    }

    body::before{
        content: "";
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.55);
    }

    .login-box{
        position: relative;
        width: 500px;
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
        margin-bottom: 10px;
        font-family: 'Cinzel', serif;
        font-size: 42px;
        font-weight: 700;
        letter-spacing: 3px;
        color: #d4af37;
    }

    .subtitle{
        text-align: center;
        margin-bottom: 40px;
        font-size: 15px;
        color: rgba(255,255,255,0.75);
        letter-spacing: 1px;
    }

    .input-box{
        margin-bottom: 25px;
    }

    .input-box label{
        display: block;
        margin-bottom: 10px;
        font-size: 15px;
        color: #f1f1f1;
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

    .btn-login{
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

    .btn-login:hover{
        background: #a7861f;
        transform: scale(1.02);
    }

    .crear-cuenta{
        text-align: center;
        margin-top: 25px;
        font-size: 14px;
        color: rgba(255,255,255,0.8);
    }

    .crear-cuenta a{
        color: #d4af37;
        text-decoration: none;
        font-weight: 500;
    }

    .crear-cuenta a:hover{
        text-decoration: underline;
    }
</style>
@endsection

@section('content')
<div class="login-box">
    <div class="logo">Machin Barber</div>
    <div class="subtitle">Iniciar Sesión</div>

    <form action="#" method="POST">
        @csrf
        <div class="input-box">
            <label>Usuario / Correo</label>
            <input type="text" name="username" placeholder="Ingresa tu usuario" required>
        </div>

        <div class="input-box">
            <label>Contraseña</label>
            <input type="password" name="password" placeholder="Ingresa tu contraseña" required>
        </div>

        <button type="submit" class="btn-login">Entrar</button>
    </form>

    <div class="crear-cuenta">
        ¿No tienes cuenta?
        <a href="{{ route('register') }}">Crear cuenta</a>
    </div>
</div>
@endsection