<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Machin Barber</title>

<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght=500;700&family=Poppins:wght=300;400;500;600&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    scroll-behavior:smooth;
}

body{
    background:#15110c;
    font-family:'Poppins', sans-serif;
    color:#f3ead9;
}

header{
    width:100%;
    position:fixed;
    top:0;
    left:0;
    z-index:1000;
    background:rgba(0,0,0,0.65);
    backdrop-filter:blur(10px);
    border-bottom:1px solid rgba(255,255,255,0.08);
}

.navbar{
    width:90%;
    margin:auto;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:18px 0;
}



.logo{
    font-family:'Cinzel', serif;
    font-size:32px;
    color:#d4af37;
    letter-spacing:3px;
}

.cart-link{ position:relative; display:flex; align-items:center; gap:7px; }
.cart-count{ min-width:20px; height:20px; display:flex; justify-content:center; align-items:center; padding:0 5px; border-radius:50%; background:#d4af37; color:#000; font-size:11px; font-weight:700; }

.nav-links{
    display:flex;
    align-items:center;
    gap:35px;
}

.nav-links a{
    color:white;
    text-decoration:none;
    transition:0.3s;
}

.nav-links a:hover{
    color:#d4af37;
}

.btn-reservar{
    background:#d4af37;
    color:black !important;
    padding:12px 24px;
    border-radius:12px;
    font-weight:600;
}
.logout-link{ background:none; border:none; color:#ffffff; font:inherit; cursor:pointer; padding:0; transition:0.3s; }
.logout-link:hover{
    color: #c9a227;
}

.hero{
    height:100vh;
    position:relative;
    overflow:hidden;
}

.slides{
    width:300%;
    height:100%;
    display:flex;
    animation:slide 15s infinite;
}

.slide{
    width:100%;
    height:100vh;
    background-size:cover;
    background-position:center;
    position:relative;
}

.slide::before{
    content:"";
    position:absolute;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.55);
}

.slide1{ background-image:url('https://maonzbarbershop.com/cdn/shop/articles/5-accesorios-imprescindibles-que-no-pueden-faltar-en-tu-barberia-483798.jpg?v=1744622379'); }
.slide2{ background-image:url('https://joseppons.com/formacion/wp-content/uploads/2021/05/estudiar-barberia.jpg'); }
.slide3{ background-image:url('https://www.shutterstock.com/image-photo/professional-barber-tools-laid-out-600nw-2447454089.jpg'); }

@keyframes slide{
    0%{ transform:translateX(0); }
    30%{ transform:translateX(0); }
    35%{ transform:translateX(-100vw); }
    65%{ transform:translateX(-100vw); }
    70%{ transform:translateX(-200vw); }
    100%{ transform:translateX(-200vw); }
}

.hero-content{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%, -50%);
    text-align:center;
    z-index:10;
    width:90%;
}

.hero-content h1{
    font-family:'Cinzel', serif;
    font-size:70px;
    color:#d4af37;
    margin-bottom:20px;
}

.hero-content p{
    font-size:22px;
    color:rgba(255,255,255,0.85);
    margin-bottom:35px;
}

.hero-buttons{
    display:flex;
    justify-content:center;
    gap:20px;
    flex-wrap:wrap;
}

.hero-buttons a{
    text-decoration:none;
    padding:15px 30px;
    border-radius:12px;
}

.btn-primary{ background:#d4af37; color:black; font-weight:600; }
.btn-secondary{ border:1px solid rgba(255,255,255,0.3); background:rgba(255,255,255,0.05); color:white; transition:0.3s; }
.hero-buttons a.btn-secondary:hover{ border-color:#7c2530; background:rgba(124,37,48,0.12); }

.about-section{ padding:100px 10%; background:#1c1610; }
.about-container{ display:flex; justify-content:space-between; align-items:center; gap:60px; flex-wrap:wrap; }
.about-text{ flex:1; }
.about-text h2{ font-family:'Cinzel', serif; font-size:45px; color:#d4af37; margin-bottom:20px; }
.about-text p{ color:rgba(255,255,255,0.75); line-height:1.8; }
.about-image img{ width:520px; height:350px; object-fit:cover; border-radius:20px; }

.services-section {
    padding: 100px 10%;
    background: #08090b;
}

.services-title {
    text-align: center;
    font-family: 'Cinzel', serif;
    font-size: 45px;
    color: #d4af37;
    margin-bottom: 60px;
}

.cards-container {
    display: grid;
    grid-template-columns: repeat(10, 1fr);
    gap: 30px;
}

.service-card {
    grid-column: span 2;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid rgba(212, 175, 55, 0.15);
    transition: transform 0.3s ease, border-color 0.3s ease;
}

/* Hace que los últimos 4 queden centrados */
.service-card:nth-child(6) {
    grid-column: 2 / span 2;
}

.service-card:hover {
    transform: translateY(-8px);
    border-color: rgba(212, 175, 55, 0.6);
}

.service-card img {
    width: 100%;
    height: 240px;
    object-fit: cover;
    display: block;
}

.card-content {
    padding: 25px;
}

.card-content h3 {
    color: #d4af37;
    margin-bottom: 12px;
    font-size: 1.3rem;
}

.card-content ul {
    list-style: none;
    padding: 0;
    margin: 0 0 20px;
    min-height: 78px;
}

.card-content ul li {
    color: rgba(255, 255, 255, 0.75);
    margin-bottom: 9px;
    line-height: 1.5;
    padding-left: 18px;
    position: relative;
}

.card-content ul li::before {
    content: "•";
    position: absolute;
    left: 0;
    color: #d4af37;
}

.service-price {
    display: block;
    color: #d4af37;
    font-size: 1.2rem;
    font-weight: bold;
    margin-top: 15px;
}

@media (max-width: 1200px) {
    .cards-container {
        grid-template-columns: repeat(3, 1fr);
    }

    .service-card,
    .service-card:nth-child(6) {
        grid-column: auto;
    }
}

@media (max-width: 768px) {
    .cards-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
}

@media (max-width: 550px) {
    .cards-container {
        grid-template-columns: 1fr;
    }
}

.booking-section{ padding:100px 10%; background:#1c1610; }
.booking-title{ text-align:center; font-family:'Cinzel', serif; font-size:45px; color:#d4af37; margin-bottom:15px; }
.booking-subtitle{ text-align:center; color:rgba(255,255,255,0.7); margin-bottom:50px; }
.booking-container{ max-width:950px; margin:auto; background:rgba(255,255,255,0.05); border-radius:25px; padding:40px; border:1px solid rgba(255,255,255,0.08); }

.chairs-title{ color:#d4af37; font-size:24px; margin-bottom:20px; }
.chairs-slider-container{ display:flex; justify-content:center; align-items:center; gap:20px; }
.chair-arrow{ width:55px; height:55px; border:none; border-radius:12px; background:rgba(255,255,255,0.08); color:white; font-size:24px; cursor:pointer; }
.chair-slide-wrapper{ width:320px; }
.chair-slide{ display:none; width:100%; height:140px; border-radius:20px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08); align-items:center; justify-content:center; font-size:34px; font-weight:600; color:#d4af37; }
.active-chair{ display:flex; }
.selected-chair-text{ text-align:center; margin-top:20px; color:rgba(255,255,255,0.7); }
.selected-chair-text span{ color:#d4af37; font-weight:600; }

.calendar-section{ margin-top:50px; max-width:720px; margin-left:auto; margin-right:auto; padding:28px; background:#15110c; border:1px solid rgba(255,255,255,0.08); border-radius:22px; }
.calendar-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; }
.calendar-header h3{ color:#d4af37; font-family:'Cinzel', serif; font-size:25px; letter-spacing:1px; text-transform:capitalize; }
.calendar-header button{ width:42px; height:42px; display:flex; justify-content:center; align-items:center; border:1px solid rgba(212,175,55,0.25); border-radius:50%; background:rgba(212,175,55,0.06); color:#d4af37; font-size:18px; cursor:pointer; transition:0.2s; }
.calendar-header button:hover{ background:#d4af37; color:#0d0d0d; transform:scale(1.05); }
.calendar-days{ display:grid; grid-template-columns:repeat(7,1fr); gap:8px; margin-bottom:10px; }
.calendar-days div{ text-align:center; color:rgba(255,255,255,0.45); font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:1px; }
.calendar-dates{ display:grid; grid-template-columns:repeat(7,1fr); gap:8px; }
.date{ position:relative; aspect-ratio:1/1; max-height:62px; display:flex; justify-content:center; align-items:center; border:1px solid transparent; border-radius:50%; background:transparent; color:rgba(255,255,255,0.85); font-size:15px; font-weight:500; cursor:pointer; transition:0.2s; }
.date:not(.past):hover{ background:rgba(212,175,55,0.10); border-color:rgba(212,175,55,0.35); color:#d4af37; }
.date.active{ background:#d4af37; color:#0d0d0d; font-weight:700; box-shadow:0 5px 20px rgba(212,175,55,0.20); }
.date.today:not(.active){ border-color:#d4af37; color:#d4af37; }
.date.today:not(.active)::after{ content:""; position:absolute; bottom:7px; width:4px; height:4px; border-radius:50%; background:#d4af37; }
.date.past{ color:rgba(255,255,255,0.18); cursor:not-allowed; }
@media(max-width:600px){
    .calendar-section{ padding:20px 14px; }
    .calendar-header h3{ font-size:20px; }
    .calendar-days, .calendar-dates{ gap:4px; }
    .date{ font-size:14px; }
}

.products-section{ padding:100px 10%; background:#15110c; }
.products-header{ max-width:1200px; margin:0 auto 50px; text-align:center; }
.products-title{ font-family:'Cinzel',serif; font-size:45px; color:#d4af37; margin-bottom:12px; }
.products-subtitle{ color:rgba(255,255,255,0.60); font-size:16px; }
.products-container{ max-width:1200px; margin:auto; display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:28px; }
.product-card{ background:#1f1810; border:1px solid rgba(255,255,255,0.08); border-radius:20px; overflow:hidden; transition:0.3s; }
.product-card:hover{ transform:translateY(-6px); border-color:rgba(212,175,55,0.30); }
.product-image-container{ width:100%; height:240px; background:#1c1610; overflow:hidden; }
.product-image{ width:100%; height:100%; object-fit:cover; transition:0.3s; }
.product-card:hover .product-image{ transform:scale(1.04); }
.product-no-image{ width:100%; height:100%; display:flex; justify-content:center; align-items:center; color:rgba(255,255,255,0.30); font-size:14px; }
.product-content{ padding:22px; }
.product-category{ display:block; color:rgba(255,255,255,0.40); font-size:12px; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px; }
.product-name{ color:white; font-size:19px; font-weight:600; margin-bottom:10px; }
.product-description{ color:rgba(255,255,255,0.55); font-size:14px; line-height:1.5; min-height:42px; margin-bottom:20px; }
.product-footer{ display:flex; justify-content:space-between; align-items:center; gap:15px; }
.product-price{ color:#d4af37; font-size:21px; font-weight:700; }
.add-cart-btn{ padding:11px 16px; border:none; border-radius:10px; background:#d4af37; color:#000; font-size:13px; font-weight:700; cursor:pointer; transition:0.2s; }
.add-cart-btn:hover{ transform:translateY(-2px); }
.add-cart-btn:disabled{ opacity:0.55; cursor:not-allowed; transform:none; }
.products-loading{ grid-column:1/-1; text-align:center; color:rgba(255,255,255,0.50); padding:40px; }
.products-empty{ grid-column:1/-1; text-align:center; padding:50px; color:rgba(255,255,255,0.50); background:#1f1810; border-radius:18px; }

@media(max-width:700px){
    .products-section{ padding:70px 6%; }
    .products-title{ font-size:34px; }
    .products-container{ grid-template-columns:1fr; }
}

.reserve-button-container{ display:flex; justify-content:center; margin-top:40px; }
.open-modal-btn{ padding:16px 45px; border:none; border-radius:14px; background:#d4af37; color:black; font-size:18px; font-weight:600; cursor:pointer; }

.booking-modal{ position:fixed; top:0; left:0; width:100%; height:100vh; background:rgba(0,0,0,0.75); display:flex; justify-content:center; align-items:center; opacity:0; visibility:hidden; transition:0.3s; z-index:99999; }
.booking-modal.active{ opacity:1; visibility:visible; }

.modal-content{ width:90%; max-width:850px; max-height:90vh; overflow-y:auto; background:#1c1610; border:1px solid rgba(212,175,55,0.15); border-radius:25px; padding:45px; position:relative; box-shadow:0 25px 70px rgba(0,0,0,0.55); }

.close-modal{ position:absolute; top:20px; right:20px; width:45px; height:45px; border:none; border-radius:12px; background:rgba(255,255,255,0.08); color:white; font-size:20px; cursor:pointer; }

.modal-title{ text-align:center; color:#d4af37; font-family:'Cinzel',serif; font-size:38px; margin-bottom:35px; }

.booking-form{ display:grid; grid-template-columns:1fr 1fr; gap:28px; }

.input-group{ display:flex; flex-direction:column; }
.input-group label{ margin-bottom:10px; color:#d4af37; }
.input-group input, .input-group select, .input-group textarea{ padding:15px; border:none; border-radius:12px; background:rgb(0, 0, 0); color:rgb(255, 253, 253); outline:none; }
.input-group textarea{ height:120px; resize:none; }
.full-width{ grid-column:span 2; }
.booking-btn{ grid-column:span 2; padding:16px; border:none; border-radius:14px; background:#d4af37; color:black; font-weight:600; cursor:pointer; }

.services-selector{ display:grid; grid-template-columns:repeat(2,1fr); gap:14px; }

.booking-service-card{ box-sizing:border-box; position:relative; width:100%; min-height:95px; display:block; padding:18px 20px; margin:0 !important; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.10); border-radius:16px; cursor:pointer; transition:0.2s; }

.booking-service-card:hover{ border-color:rgba(212,175,55,0.45); background:rgba(212,175,55,0.06); }

.booking-service-card:has(.service-checkbox:checked){ border-color:#d4af37; background:rgba(212,175,55,0.10); }

.service-checkbox{
    width:18px;
    height:18px;
    accent-color:#d4af37;
    cursor:pointer;
}
.service-card-content{ width:100%; display:flex; flex-direction:column; gap:14px; }

.service-card-name{ display:flex; align-items:center; gap:10px; color:white; font-size:16px; font-weight:500; }

.service-card-name{ display:block; color:white; font-size:16px; font-weight:500; }

.booking-service-card:has(.service-checkbox:checked) .service-card-name{ color:#d4af37; }

.service-card-info{ width:100%; display:flex; justify-content:space-between; align-items:center; }

.service-card-info span{ color:rgba(255,255,255,0.50); font-size:13px; }

.service-card-info strong{ color:#d4af37; font-size:15px; font-weight:600; }
.booking-summary{ margin-top:16px; padding:18px 20px; display:flex; justify-content:space-between; align-items:center; background:rgba(212,175,55,0.08); border:1px solid rgba(212,175,55,0.20); border-radius:14px; }

.summary-item{ display:flex; flex-direction:column; gap:4px; }

.summary-item span{ color:rgba(255,255,255,0.60); font-size:13px; }

.summary-item strong{ color:#d4af37; font-size:18px; }

.summary-item strong span{ color:#d4af37; font-size:18px; }

.summary-divider{ width:1px; height:38px; background:rgba(212,175,55,0.25); }

.time-summary{ margin-top:10px; padding:12px 14px; background:rgba(212,175,55,0.08); border:1px solid rgba(212,175,55,0.18); border-radius:10px; }

.time-summary span{ color:rgba(255,255,255,0.60); font-size:13px; }

.time-summary strong{ color:#d4af37; font-size:14px; }

.optional-text{ color:rgba(255,255,255,0.40); font-size:12px; font-weight:400; }

@media(max-width:700px){
    .services-selector{ grid-template-columns:1fr; }
}
</style>
</head>
<body>

<header>
    <nav class="navbar">

        <div class="logo">
            Machin Barber
        </div>

        <div class="nav-links">

            <a href="#">
                Galería
            </a>

            <a href="#services">
                Servicios
            </a>

            <a href="#products">
                Productos
            </a>

            @auth

                <a href="{{ route('appointments.mine') }}">
                    Mis citas
                </a>

                <a href="{{ route('purchases.mine') }}">
                    Mis compras
                </a>

            @endauth

            <a href="{{ route('cart.view') }}" class="cart-link">
                Carrito
                <span id="cartCount" class="cart-count">0</span>
            </a>

            @guest

                <a href="{{ route('login') }}">
                    Iniciar sesión
                </a>

            @endguest

            @auth

                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf

                    <button type="submit" class="logout-link">
                        Cerrar sesión
                    </button>

                </form>

            @endauth

            <a href="#booking" class="btn-reservar">
                Reservar
            </a>

        </div>

    </nav>
</header>

<section class="hero">
    <div class="slides">
        <div class="slide slide1"></div>
        <div class="slide slide2"></div>
        <div class="slide slide3"></div>
    </div>
    <div class="hero-content">
        <h1>Estilo que impone presencia</h1>
        <p>Más que un corte, una experiencia.</p>
        <div class="hero-buttons">
            <a href="#booking" class="btn-primary">Reservar cita</a>
            <a href="#" class="btn-secondary">Ver servicios</a>
        </div>
    </div>
</section>

<section class="services-section" id="services">
    <h2 class="services-title">Nuestros Servicios</h2>

    @php
        $serviceImages = [
            'Afeitado clásico' => 'https://images.unsplash.com/photo-1621605815971-fbc98d665033?q=80&w=1200&auto=format&fit=crop',

            'Barba & Estilo' => 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=1200&auto=format&fit=crop',

            'Corte clásico' => 'https://images.unsplash.com/photo-1599351431202-1e0f0137899a?q=80&w=1200&auto=format&fit=crop',

            'Corte niño' => 'https://revistaflow.com/wp-content/uploads/2025/08/Inicio-de-clases-2025-Cortes-de-cabello-para-ninos-696x461.jpg',

            'Fade moderno' => 'https://www.clara.es/medio/2023/07/10/cortes-de-pelo-degradados-para-hombre-mid-fade_fb12bf87_230710151957_800x996.jpg',
            'Limpieza facial' => 'https://homme.mx/wp-content/uploads/2023/09/tratamientos-faciales-hombres-homme-luxury-barbers-01.jpg ',
            'Perfilado de cejas' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRbNgI-njoxse2rWCSHcxkG2o-G__iUwJzotUGaN-cY6w733KsWHFJA9T_H&s=10',

            'Ritual Machin' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTZh2jRxqEsWkLJ5bcu_HSzzdcoqSl0qQ2F86__3BOx4USHgRzxZJDnDjY&s=10',

            'Spa Premium' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQaJmy2aQMM5XgLWGJ3AQMHHKDIDpFdZjCfh2mM9Tjdy56ba-xZmo7MXIA&s=10',
        ];
    @endphp

    <div class="cards-container">
        @foreach($services as $service)
            <div class="service-card">

                <img
                    src="{{ $serviceImages[$service->name] ?? 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=1200&auto=format&fit=crop' }}"
                    alt="{{ $service->name }}"
                >

                <div class="card-content">
                    <h3>{{ $service->name }}</h3>

                    <ul>
                        @foreach(preg_split('/\s*,\s*/', $service->description) as $item)
                            <li>{{ trim($item) }}</li>
                        @endforeach
                    </ul>

                    <span class="service-price">
                        ${{ number_format($service->price, 2) }}
                    </span>
                </div>

            </div>
        @endforeach
    </div>
</section>

<section class="products-section" id="products">
    <div class="products-header">
        <div>
            <h2 class="products-title">Nuestros Productos</h2>
            <p class="products-subtitle">Productos seleccionados para mantener tu estilo todos los días.</p>
        </div>
    </div>

    <div id="productsContainer" class="products-container">
        <p class="products-loading">Cargando productos...</p>
    </div>
</section>

<section class="booking-section" id="booking">
    <h2 class="booking-title">Reserva Tu Cita</h2>
    <p class="booking-subtitle">Selecciona tu silla y fecha.</p>
    <div class="booking-container">
        <h3 class="chairs-title">Selecciona tu silla</h3>
        <div class="chairs-slider-container">
            <button class="chair-arrow" id="prevChair">❮</button>
           <div class="chair-slide-wrapper" id="chairSlides">
    <!-- Las sillas se cargarán desde /api/chairs -->
</div>
            <button class="chair-arrow" id="nextChair">❯</button>
        </div>
       <p class="selected-chair-text">
    Silla seleccionada:
    <span id="selectedChair">Cargando...</span>
</p>

        <div class="calendar-section">
            <div class="calendar-header">
                <button id="prevMonth">❮</button>
                <h3 id="monthYear"></h3>
                <button id="nextMonth">❯</button>
            </div>
            <div class="calendar-days">
    <div>Lun</div>
    <div>Mar</div>
    <div>Mié</div>
    <div>Jue</div>
    <div>Vie</div>
    <div>Sáb</div>
    <div>Dom</div>
</div>
            <div class="calendar-dates" id="calendarDates"></div>
        </div>

        <div class="reserve-button-container">
            <button type="button" id="openBookingModal" class="open-modal-btn">Reservar</button>
        </div>
    </div>
</section>

<div class="booking-modal" id="bookingModal">
    <div class="modal-content">
        <button type="button" class="close-modal" id="closeModal">✕</button>
        <h2 class="modal-title">Completa tu reserva</h2>
        
        <form class="booking-form" id="bookingForm">
    @csrf

    <div class="input-group">
    <label>Nombre Completo</label>

    @auth
        <input
            type="text"
            id="bookingName"
            value="{{ auth()->user()->client?->person?->name }} {{ auth()->user()->client?->person?->last_name }}"
            readonly
        >
    @else
        <input
            type="text"
            id="bookingName"
            value=""
            placeholder="Debes iniciar sesión"
            readonly
        >
    @endauth
</div>

 <div class="input-group services-group">
    <label>Selecciona tus servicios</label>

    <div id="servicesContainer" class="services-selector">
        <p>Cargando servicios...</p>
    </div>

    <div id="bookingSummary" class="booking-summary" style="display:none;">
        <div class="summary-item">
            <span>Duración estimada</span>
            <strong id="totalDuration">0 min</strong>
        </div>

        <div class="summary-divider"></div>

        <div class="summary-item">
            <span>Total</span>
            <strong>$<span id="totalPrice">0.00</span></strong>
        </div>
    </div>
</div>

<div class="input-group">
    <label>Hora disponible</label>

    <select id="timeSelect" required disabled>
        <option value="">Selecciona una hora</option>
    </select>

    <div id="appointmentTimeSummary" class="time-summary" style="display:none;">
        <span>Tu cita terminará aproximadamente a las</span>
        <strong id="appointmentTime"></strong>
    </div>
</div>

<div class="input-group full-width">
    <label>Comentarios <span class="optional-text">(opcional)</span></label>

    <textarea
        id="bookingNotes"
        placeholder="¿Alguna indicación especial para tu barbero?"
    ></textarea>
</div>

    <button
        type="submit"
        class="booking-btn"
        id="bookingSubmit"
    >
        Confirmar Reserva
    </button>
</form>
<script>
document.addEventListener("DOMContentLoaded", () => {

    // =====================================================
    // VARIABLES GENERALES
    // =====================================================

    const bookingForm = document.getElementById("bookingForm");
    const bookingNotes = document.getElementById("bookingNotes");
    const bookingSubmit = document.getElementById("bookingSubmit");

    const servicesContainer = document.getElementById("servicesContainer");

    const bookingSummary = document.getElementById("bookingSummary");
    const totalDurationElement = document.getElementById("totalDuration");
    const totalPriceElement = document.getElementById("totalPrice");

    const timeSelect = document.getElementById("timeSelect");

    const appointmentTimeSummary =
        document.getElementById("appointmentTimeSummary");

    const appointmentTime =
        document.getElementById("appointmentTime");

    const monthYear = document.getElementById("monthYear");
    const calendarDates = document.getElementById("calendarDates");
    const prevMonth = document.getElementById("prevMonth");
    const nextMonth = document.getElementById("nextMonth");

    const chairSlidesContainer = document.getElementById("chairSlides");
    const prevChair = document.getElementById("prevChair");
    const nextChair = document.getElementById("nextChair");
    const selectedChair = document.getElementById("selectedChair");

    const bookingModal = document.getElementById("bookingModal");
    const openBookingModal = document.getElementById("openBookingModal");
    const closeModal = document.getElementById("closeModal");


    // =====================================================
    // ESTADO DE LA RESERVA
    // =====================================================

    let chairs = [];
    let currentChair = 0;

    let selectedChairData = null;
    let selectedDate = null;
    let selectedAvailability = [];

    let currentDate = new Date();


    // =====================================================
    // CARGAR SILLAS DESDE LA API
    // =====================================================

    async function loadChairs() {

        try {

            const response = await fetch('/api/chairs');

            if (!response.ok) {
                throw new Error('No se pudieron cargar las sillas');
            }

            const result = await response.json();

            chairs = result.data.data;

            chairSlidesContainer.innerHTML = '';

            chairs.forEach((chair, index) => {

                const element = document.createElement('div');

                element.classList.add('chair-slide');

                element.textContent = chair.chairName;

                element.addEventListener('click', () => {
                    currentChair = index;
                    showChair(currentChair);
                });

                chairSlidesContainer.appendChild(element);
            });

            if (chairs.length > 0) {

                currentChair = 0;

                showChair(currentChair);
            }

        } catch (error) {

            console.error('Error cargando sillas:', error);

            selectedChair.textContent = 'Error al cargar sillas';
        }
    }


    // =====================================================
    // MOSTRAR SILLA SELECCIONADA
    // =====================================================

    function showChair(index) {

        if (chairs.length === 0) {
            return;
        }

        const chairSlides =
            document.querySelectorAll(".chair-slide");

        chairSlides.forEach(slide => {
            slide.classList.remove("active-chair");
        });

        if (chairSlides[index]) {
            chairSlides[index].classList.add("active-chair");
        }

        selectedChairData = chairs[index];

        selectedChair.textContent =
            selectedChairData.chairName;

        loadServicesForChair();

        resetTimes();
    }


    // =====================================================
    // FLECHAS DE LAS SILLAS
    // =====================================================

    nextChair.addEventListener("click", () => {

        if (chairs.length === 0) {
            return;
        }

        currentChair++;

        if (currentChair >= chairs.length) {
            currentChair = 0;
        }

        showChair(currentChair);
    });


    prevChair.addEventListener("click", () => {

        if (chairs.length === 0) {
            return;
        }

        currentChair--;

        if (currentChair < 0) {
            currentChair = chairs.length - 1;
        }

        showChair(currentChair);
    });


    // =====================================================
    // CARGAR SERVICIOS DE LA SILLA
    // =====================================================

     function loadServicesForChair(){

    servicesContainer.innerHTML = '';
    bookingSummary.style.display = 'none';
    appointmentTimeSummary.style.display = 'none';

    if(!selectedChairData){
        return;
    }

    selectedChairData.services.forEach(service => {

        const label = document.createElement("label");
        label.classList.add("booking-service-card");

        const checkbox = document.createElement("input");

        checkbox.type = "checkbox";
        checkbox.classList.add("service-checkbox");
        checkbox.value = service.serviceID;
        checkbox.dataset.price = service.price;
        checkbox.dataset.duration = service.aproxDuration;

        const content = document.createElement("div");
        content.classList.add("service-card-content");

        const name = document.createElement("span");
        name.classList.add("service-card-name");
        name.textContent = service.name;

        const info = document.createElement("div");
        info.classList.add("service-card-info");

        const duration = document.createElement("span");
        duration.textContent = `${service.aproxDuration} min`;

        const price = document.createElement("strong");
        price.textContent = `$${Number(service.price).toFixed(2)}`;

        info.appendChild(duration);
        info.appendChild(price);

        content.appendChild(name);
        content.appendChild(info);

        label.appendChild(checkbox);
        label.appendChild(content);

        checkbox.addEventListener("change", () => {
            updateServiceSummary();
            loadAvailability();
        });

        servicesContainer.appendChild(label);
    });
}

    // =====================================================
    // OBTENER SERVICIOS SELECCIONADOS
    // =====================================================

    function getSelectedServices() {

        const checkboxes =
            document.querySelectorAll(
                ".service-checkbox:checked"
            );

        return Array.from(checkboxes).map(
            checkbox => {

                return {

                    serviceID:
                        Number(checkbox.value),

                    price:
                        Number(
                            checkbox.dataset.price
                        ),

                    duration:
                        Number(
                            checkbox.dataset.duration
                        )
                };
            }
        );
    }


    // =====================================================
    // MOSTRAR RESUMEN DE SERVICIOS
    // =====================================================

    function updateServiceSummary() {

        const services =
            getSelectedServices();

        if (services.length === 0) {

            bookingSummary.style.display =
                "none";

            return;
        }

        const totalDuration =
            services.reduce(
                (total, service) =>
                    total + service.duration,
                0
            );

        const totalPrice =
            services.reduce(
                (total, service) =>
                    total + service.price,
                0
            );

        totalDurationElement.textContent =
            `${totalDuration} min`;

        totalPriceElement.textContent =
            totalPrice.toFixed(2);

        bookingSummary.style.display =
            "flex";
    }


    // =====================================================
    // CALENDARIO
    // =====================================================

  
    // =====================================================
// NAVEGACIÓN DEL CALENDARIO
// =====================================================
function renderCalendar(){

    calendarDates.innerHTML = "";

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const jsFirstDay = new Date(year, month, 1).getDay();
    const firstDay = (jsFirstDay + 6) % 7;
    const lastDate = new Date(year, month + 1, 0).getDate();

    const months = [
        "Enero","Febrero","Marzo","Abril","Mayo","Junio",
        "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"
    ];

    monthYear.textContent = `${months[month]} ${year}`;

    for(let i = 0; i < firstDay; i++){
        calendarDates.appendChild(document.createElement("div"));
    }

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    for(let day = 1; day <= lastDate; day++){

        const date = document.createElement("div");

        date.classList.add("date");
        date.textContent = day;

        const cellDate = new Date(year, month, day);

        cellDate.setHours(0, 0, 0, 0);

        if(cellDate.getTime() === today.getTime()){
            date.classList.add("today");
        }

        if(cellDate < today){

            date.classList.add("past");

        }else{

            date.addEventListener("click", () => {

                document
                    .querySelectorAll(".date")
                    .forEach(d => d.classList.remove("active"));

                date.classList.add("active");

                const yyyy = year;
                const mm = String(month + 1).padStart(2, "0");
                const dd = String(day).padStart(2, "0");

                selectedDate = `${yyyy}-${mm}-${dd}`;

                loadAvailability();
            });
        }

        calendarDates.appendChild(date);
    }
}

// =====================================================
// NAVEGACIÓN DEL CALENDARIO
// =====================================================

prevMonth.addEventListener("click", () => {

    const today = new Date();

    const currentYear = currentDate.getFullYear();
    const currentMonth = currentDate.getMonth();

    const todayYear = today.getFullYear();
    const todayMonth = today.getMonth();

    // No permitir navegar a meses anteriores al actual
    if(
        currentYear === todayYear &&
        currentMonth === todayMonth
    ){
        return;
    }

    currentDate.setDate(1);

    currentDate.setMonth(
        currentDate.getMonth() - 1
    );

    renderCalendar();
});


nextMonth.addEventListener("click", () => {

    currentDate.setDate(1);

    currentDate.setMonth(
        currentDate.getMonth() + 1
    );

    renderCalendar();
});

// =====================================================
// CONSULTAR DISPONIBILIDAD
// =====================================================


    async function loadAvailability() {

        const selectedServices =
            getSelectedServices();

        if (
            !selectedChairData ||
            selectedServices.length === 0 ||
            !selectedDate
        ) {

            resetTimes();

            return;
        }

        timeSelect.disabled = true;

        appointmentTimeSummary.style.display =
            "none";

        timeSelect.innerHTML = `
            <option value="">
                Consultando horarios...
            </option>
        `;

        try {

            const params =
                new URLSearchParams();

            params.append(
                "chairID",
                selectedChairData.chairID
            );

            params.append(
                "date",
                selectedDate
            );

            selectedServices.forEach(
                service => {

                    params.append(
                        "serviceIDs[]",
                        service.serviceID
                    );
                }
            );

            const response =
                await fetch(
                    `/api/appointments/availability?${params.toString()}`
                );

            const result =
                await response.json();

            if (!response.ok) {

                throw new Error(
                    result.message ||
                    "Error consultando disponibilidad"
                );
            }

            selectedAvailability =
                result.available || [];

            /*
            |--------------------------------------------------------------------------
            | Usar totales calculados por Laravel
            |--------------------------------------------------------------------------
            */

            if (result.totalDuration !== undefined) {

                totalDurationElement.textContent =
                    `${result.totalDuration} min`;

                totalPriceElement.textContent =
                    Number(
                        result.totalPrice
                    ).toFixed(2);

                bookingSummary.style.display =
                    "flex";
            }

            if (
                selectedAvailability.length === 0
            ) {

                timeSelect.innerHTML = `
                    <option value="">
                        No hay horarios disponibles
                    </option>
                `;

                timeSelect.disabled = true;

                return;
            }

            timeSelect.innerHTML = `
                <option value="">
                    Selecciona una hora
                </option>
            `;

            selectedAvailability.forEach(
                (slot, index) => {

                    const option =
                        document.createElement(
                            "option"
                        );

                    option.value = index;

                    option.textContent =
                        `${formatTime(slot.time)} - ${slot.employee}`;

                    timeSelect.appendChild(
                        option
                    );
                }
            );

            timeSelect.disabled = false;

        } catch (error) {

            console.error(
                "Error:",
                error
            );

            selectedAvailability = [];

            timeSelect.innerHTML = `
                <option value="">
                    Error al consultar horarios
                </option>
            `;

            timeSelect.disabled = true;
        }
    }


    // =====================================================
    // MOSTRAR HORA DE INICIO Y FIN
    // =====================================================

    timeSelect.addEventListener(
        "change",
        () => {

            if (timeSelect.value === "") {

                appointmentTimeSummary.style.display =
                    "none";

                return;
            }

            const index =
                parseInt(timeSelect.value);

            const slot =
                selectedAvailability[index];

            if (!slot) {

                appointmentTimeSummary.style.display =
                    "none";

                return;
            }

            const start =
                slot.startHour.substring(11, 16);

            const finish =
                slot.finishHour.substring(11, 16);

            appointmentTime.textContent =
                `${formatTime(start)} - ${formatTime(finish)}`;

            appointmentTimeSummary.style.display =
                "block";
        }
    );


    // =====================================================
    // CONVERTIR 24H A AM/PM
    // =====================================================

    function formatTime(time) {

        const [hour, minute] =
            time.split(":");

        let h =
            parseInt(hour);

        const ampm =
            h >= 12
                ? "PM"
                : "AM";

        h =
            h % 12 || 12;

        return `${h}:${minute} ${ampm}`;
    }


    // =====================================================
    // LIMPIAR HORARIOS
    // =====================================================

    function resetTimes() {

        selectedAvailability = [];

        timeSelect.disabled = true;

        timeSelect.innerHTML = `
            <option value="">
                Primero selecciona servicios y fecha
            </option>
        `;

        appointmentTimeSummary.style.display =
            "none";
    }


    // =====================================================
    // MODAL
    // =====================================================

    openBookingModal.addEventListener(
        "click",
        () => {

            bookingModal.classList.add(
                "active"
            );
        }
    );


    closeModal.addEventListener(
        "click",
        () => {

            bookingModal.classList.remove(
                "active"
            );
        }
    );


    bookingModal.addEventListener(
        "click",
        (e) => {

            if (e.target === bookingModal) {

                bookingModal.classList.remove(
                    "active"
                );
            }
        }
    );


    // =====================================================
    // CREAR RESERVA
    // =====================================================

    bookingForm.addEventListener(
        "submit",
        async (e) => {

            e.preventDefault();

            // ---------------------------------------------
            // Validar silla
            // ---------------------------------------------

            if (!selectedChairData) {

                alert(
                    "Selecciona una silla."
                );

                return;
            }


            // ---------------------------------------------
            // Validar servicios
            // ---------------------------------------------

            const selectedServices =
                getSelectedServices();

            if (
                selectedServices.length === 0
            ) {

                alert(
                    "Selecciona al menos un servicio."
                );

                return;
            }


            // ---------------------------------------------
            // Validar horario
            // ---------------------------------------------

            if (timeSelect.value === "") {

                alert(
                    "Selecciona un horario."
                );

                return;
            }


            const availabilityIndex =
                parseInt(
                    timeSelect.value
                );


            const selectedSlot =
                selectedAvailability[
                    availabilityIndex
                ];


            if (!selectedSlot) {

                alert(
                    "El horario seleccionado ya no es válido."
                );

                return;
            }


            // ---------------------------------------------
            // Crear servicios para AppointmentController
            // ---------------------------------------------

            const services =
                selectedServices.map(
                    service => {

                        return {

                            serviceID:
                                service.serviceID,

                            totalPrice:
                                service.price
                        };
                    }
                );


            // ---------------------------------------------
            // Crear datos para AppointmentController
            // ---------------------------------------------

            const appointmentData = {

                employeeID:
                    selectedSlot.employeeID,

                chairID:
                    selectedChairData.chairID,

                startHour:
                    selectedSlot.startHour,

                services:
                    services,

                notes:
    bookingNotes
        ? bookingNotes.value.trim() || null
        : null

            };


            console.log(
                "Enviando cita:",
                appointmentData
            );


            bookingSubmit.disabled = true;

            bookingSubmit.textContent =
                "Reservando...";


            try {

                const response =
                    await fetch(
                        "/api/appointments",
                        {
                            method: "POST",

                            headers: {
                                "Content-Type":
                                    "application/json",

                                "Accept":
                                    "application/json"
                            },

                            credentials:
                                "same-origin",

                            body:
                                JSON.stringify(
                                    appointmentData
                                )
                        }
                    );


                const result =
                    await response.json();


                if (!response.ok) {

                    console.error(
                        "Error de reserva:",
                        result
                    );


                    if (result.errors) {

                        const messages =
                            Object.values(
                                result.errors
                            )
                                .flat()
                                .join("\n");

                        alert(messages);

                    } else {

                        alert(
                            result.message ||
                            "No se pudo crear la cita."
                        );
                    }

                    return;
                }


                // ---------------------------------------------
                // CITA CREADA
                // ---------------------------------------------

                console.log(
                    "Cita creada:",
                    result
                );


                alert(
                    "¡Tu cita fue reservada correctamente!"
                );


                bookingModal.classList.remove(
                    "active"
                );


                if (bookingNotes) {
    bookingNotes.value = "";
}


                document
                    .querySelectorAll(
                        ".service-checkbox"
                    )
                    .forEach(
                        checkbox => {
                            checkbox.checked =
                                false;
                        }
                    );


                bookingSummary.style.display =
                    "none";


                resetTimes();


                if (selectedDate) {

                    loadAvailability();
                }


            } catch (error) {

                console.error(
                    "Error creando cita:",
                    error
                );


                alert(
                    "Ocurrió un error al intentar reservar la cita."
                );

            } finally {

                bookingSubmit.disabled =
                    false;

                bookingSubmit.textContent =
                    "Confirmar Reserva";
            }
        }
    );

    // =====================================================
// PRODUCTOS
// =====================================================

const productsContainer = document.getElementById("productsContainer");
const cartCount = document.getElementById("cartCount");


// =====================================================
// CARGAR PRODUCTOS
// =====================================================

async function loadProducts(){

    try {

        const response = await fetch("/api/shop/products", {
            headers: {
                "Accept": "application/json"
            }
        });

        const result = await response.json();

        if(!response.ok){
            throw new Error(
                result.message ||
                "No se pudieron cargar los productos."
            );
        }

        const products = result.data.data;

        renderProducts(products);

    }catch(error){

        console.error(
            "Error cargando productos:",
            error
        );

        productsContainer.innerHTML = `
            <div class="products-empty">
                No se pudieron cargar los productos.
            </div>
        `;
    }
}


// =====================================================
// MOSTRAR PRODUCTOS
// =====================================================

function renderProducts(products) {

    productsContainer.innerHTML = "";

    if (!products || products.length === 0) {

        productsContainer.innerHTML = `
            <div class="products-empty">
                No hay productos disponibles.
            </div>
        `;

        return;
    }

    products.forEach(product => {

        const card = document.createElement("div");

        card.classList.add("product-card");

        let imageHTML = `
            <div class="product-no-image">
                Sin imagen
            </div>
        `;

        if (product.images && product.images.length > 0) {

            // Intentamos cargar las imágenes en orden.
            // Si una falla, probamos la siguiente.
            const imageURL = product.images
                .map(image => {
                    let path = image.image.replace(/\\/g, "/");

                    if (!path.startsWith("http")) {
                        path = `/storage/${path}`;
                    }

                    return path;
                })[0];

            console.log(product.name, imageURL);

            imageHTML = `
                <img
                    src="${imageURL}"
                    alt="${product.name}"
                    class="product-image"
                    onerror="this.onerror=null; this.src=''; this.parentElement.innerHTML='<div class=&quot;product-no-image&quot;>Sin imagen</div>';"
                >
            `;
        }

        const category = product.category
            ? product.category.name
            : "Producto";

        card.innerHTML = `
            <div class="product-image-container">
                ${imageHTML}
            </div>

            <div class="product-content">

                <span class="product-category">
                    ${category}
                </span>

                <h3 class="product-name">
                    ${product.name}
                </h3>

                <p class="product-description">
                    ${product.description || ""}
                </p>

                <div class="product-footer">

                    <span class="product-price">
                        $${Number(product.sell_price).toFixed(2)}
                    </span>

                    <button
                        type="button"
                        class="add-cart-btn"
                        onclick="addToCart(${product.productID}, this)"
                    >
                        Agregar
                    </button>

                </div>

            </div>
        `;

        productsContainer.appendChild(card);
    });
}


// =====================================================
// AGREGAR AL CARRITO
// =====================================================

async function addToCart(productID, button){

    const originalText = button.textContent;

    button.disabled = true;
    button.textContent = "Agregando...";

    try {

        const response = await fetch("/api/cart", {
            method: "POST",

            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },

            credentials: "same-origin",

            body: JSON.stringify({
                id: productID,
                quantity: 1
            })
        });

        const result = await response.json();

        if(response.status === 401){
            window.location.href = "{{ route('login') }}";
            return;
        }

        if(!response.ok){

            alert(
                result.message ||
                "No se pudo agregar el producto."
            );

            button.textContent = originalText;
            button.disabled = false;

            return;
        }

        button.textContent = "Agregado";

        updateCartCount(result.data);

        setTimeout(() => {

            button.textContent = originalText;
            button.disabled = false;

        }, 1000);

    }catch(error){

        console.error(
            "Error agregando producto:",
            error
        );

        button.textContent = originalText;
        button.disabled = false;

        alert(
            "Ocurrió un error al agregar el producto."
        );
    }
}

window.addToCart = addToCart;
// =====================================================
// ACTUALIZAR CONTADOR DEL CARRITO
// =====================================================

function updateCartCount(cart){

    if(!cart || !cart.producto_cart){

        cartCount.textContent = "0";

        return;
    }

    const quantity = cart.producto_cart.reduce(
        (total, item) => {
            return total + Number(item.quantity);
        },
        0
    );

    cartCount.textContent = quantity;
}


// =====================================================
// CARGAR CONTADOR DEL CARRITO
// =====================================================

async function loadCartCount(){

    try {

        const response = await fetch("/api/cart", {
            headers: {
                "Accept": "application/json"
            },

            credentials: "same-origin"
        });

        if(response.status === 401){

            cartCount.textContent = "0";

            return;
        }

        const result = await response.json();

        if(!response.ok){
            return;
        }

        updateCartCount(result.data);

    }catch(error){

        console.error(
            "Error cargando contador:",
            error
        );
    }
}


    // =====================================================
    // INICIALIZACIÓN
    // =====================================================

    renderCalendar();

    resetTimes();

    loadChairs();

    loadProducts();

    loadCartCount();

});
</script>