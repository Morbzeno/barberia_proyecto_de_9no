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
    background:#0d0d0d;
    font-family:'Poppins', sans-serif;
    color:blue;
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

.slide1{ background-image:url('https://images.unsplash.com/photo-1517832606299-7ae9b720a186?q=80&w=1920&auto=format&fit=crop'); }
.slide2{ background-image:url('https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=1920&auto=format&fit=crop'); }
.slide3{ background-image:url('https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?q=80&w=1920&auto=format&fit=crop'); }

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
.btn-secondary{ border:1px solid rgba(255,255,255,0.3); background:rgba(255,255,255,0.05); color:white; }

.about-section{ padding:100px 10%; background:#111; }
.about-container{ display:flex; justify-content:space-between; align-items:center; gap:60px; flex-wrap:wrap; }
.about-text{ flex:1; }
.about-text h2{ font-family:'Cinzel', serif; font-size:45px; color:#d4af37; margin-bottom:20px; }
.about-text p{ color:rgba(255,255,255,0.75); line-height:1.8; }
.about-image img{ width:520px; height:350px; object-fit:cover; border-radius:20px; }

.services-section{ padding:100px 10%; }
.services-title{ text-align:center; font-family:'Cinzel', serif; font-size:45px; color:#d4af37; margin-bottom:60px; }
.cards-container{ display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:30px; }
.service-card{ background:rgba(255,255,255,0.05); border-radius:20px; overflow:hidden; border:1px solid rgba(255,255,255,0.08); transition:0.3s; }
.service-card:hover{ transform:translateY(-8px); }
.service-card img{ width:100%; height:240px; object-fit:cover; }
.card-content{ padding:25px; }
.card-content h3{ color:#d4af37; margin-bottom:18px; }
.card-content ul{ list-style:none; }
.card-content ul li{ margin-bottom:10px; color:rgba(255,255,255,0.75); }

.booking-section{ padding:100px 10%; background:#111; }
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

.calendar-section{ margin-top:50px; }
.calendar-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.calendar-header h3{ color:#d4af37; }
.calendar-header button{ width:45px; height:45px; border:none; border-radius:12px; background:rgba(255,255,255,0.08); color:white; cursor:pointer; }
.calendar-days, .calendar-dates{ display:grid; grid-template-columns:repeat(7,1fr); gap:10px; }
.calendar-days div{ text-align:center; color:#d4af37; font-weight:600; }
.date{ height:70px; display:flex; justify-content:center; align-items:center; border-radius:12px; background:rgba(255,255,255,0.05); cursor:pointer; }
.date.active{ background:#d4af37; color:black; font-weight:700; }

.reserve-button-container{ display:flex; justify-content:center; margin-top:40px; }
.open-modal-btn{ padding:16px 45px; border:none; border-radius:14px; background:#d4af37; color:black; font-size:18px; font-weight:600; cursor:pointer; }

.booking-modal{ position:fixed; top:0; left:0; width:100%; height:100vh; background:rgba(0,0,0,0.75); display:flex; justify-content:center; align-items:center; opacity:0; visibility:hidden; transition:0.3s; z-index:99999; }
.booking-modal.active{ opacity:1; visibility:visible; }
.modal-content{ width:90%; max-width:700px; background:#111; border-radius:25px; padding:40px; position:relative; }
.close-modal{ position:absolute; top:20px; right:20px; width:45px; height:45px; border:none; border-radius:12px; background:rgba(255,255,255,0.08); color:white; font-size:20px; cursor:pointer; }
.modal-title{ text-align:center; color:#d4af37; font-family:'Cinzel', serif; font-size:38px; margin-bottom:35px; }

.booking-form{ display:grid; grid-template-columns:1fr 1fr; gap:25px; }
.input-group{ display:flex; flex-direction:column; }
.input-group label{ margin-bottom:10px; color:#d4af37; }
.input-group input, .input-group select, .input-group textarea{ padding:15px; border:none; border-radius:12px; background:rgb(0, 0, 0); color:rgb(255, 253, 253); outline:none; }
.input-group textarea{ height:120px; resize:none; }
.full-width{ grid-column:span 2; }
.booking-btn{ grid-column:span 2; padding:16px; border:none; border-radius:14px; background:#d4af37; color:black; font-weight:600; cursor:pointer; }
</style>
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo">Machin Barber</div>
        <div class="nav-links">
            <a href="#">Galería</a>
            <a href="#">Servicios</a>
            <a href="{{ route('login') }}">Iniciar Sesión</a>
            <a href="#booking" class="btn-reservar">Reservar</a>
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
        <p>Más que un corte, una experiencia premium.</p>
        <div class="hero-buttons">
            <a href="#booking" class="btn-primary">Reservar cita</a>
            <a href="#" class="btn-secondary">Ver servicios</a>
        </div>
    </div>
</section>

<section class="about-section">
    <div class="about-container">
        <div class="about-text">
            <h2>Tradición y Estilo</h2>
            <p>En Machin Barber combinamos técnicas clásicas con estilo moderno para ofrecer una experiencia premium.</p>
        </div>
        <div class="about-image">
            <img src="https://images.unsplash.com/photo-1622286342621-4bd786c2447c?q=80&w=1200&auto=format&fit=crop">
        </div>
    </div>
</section>

<section class="services-section">
    <h2 class="services-title">Nuestros Servicios</h2>
    <div class="cards-container">
        <div class="service-card">
            <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?q=80&w=1200&auto=format&fit=crop">
            <div class="card-content">
                <h3>Spa Premium</h3>
                <ul>
                    <li>Corte de cabello</li>
                    <li>Limpieza facial</li>
                    <li>Exfoliación facial</li>
                </ul>
            </div>
        </div>
        <div class="service-card">
            <img src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=1200&auto=format&fit=crop">
            <div class="card-content">
                <h3>Barba & Estilo</h3>
                <ul>
                    <li>Perfilado de barba</li>
                    <li>Toalla caliente</li>
                    <li>Aceites premium</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="booking-section" id="booking">
    <h2 class="booking-title">Reserva Tu Cita</h2>
    <p class="booking-subtitle">Selecciona tu silla y fecha.</p>
    <div class="booking-container">
        <h3 class="chairs-title">Selecciona tu silla</h3>
        <div class="chairs-slider-container">
            <button class="chair-arrow" id="prevChair">❮</button>
            <div class="chair-slide-wrapper">
                {{-- Ciclo Blade para renderizar las 10 sillas automáticamente --}}
                @for ($i = 1; $i <= 10; $i++)
                    <div class="chair-slide {{ $i == 1 ? 'active-chair' : '' }}">Silla {{ $i }}</div>
                @endfor
            </div>
            <button class="chair-arrow" id="nextChair">❯</button>
        </div>
        <p class="selected-chair-text">Silla seleccionada: <span id="selectedChair">Silla 1</span></p>

        <div class="calendar-section">
            <div class="calendar-header">
                <button id="prevMonth">❮</button>
                <h3 id="monthYear"></h3>
                <button id="nextMonth">❯</button>
            </div>
            <div class="calendar-days">
                <div>Dom</div><div>Lun</div><div>Mar</div><div>Mié</div><div>Jue</div><div>Vie</div><div>Sáb</div>
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
        
        <form class="booking-form" action="#" method="POST">
            @csrf
            <div class="input-group">
                <label>Nombre Completo</label>
                <input type="text" placeholder="Ingresa tu nombre" required>
            </div>
            <div class="input-group">
                <label>Servicio</label>
                <select required>
                    <option value="">Selecciona un servicio</option>
                    <option>Corte Clásico</option>
                    <option>Fade Moderno</option>
                    <option>Barba Premium</option>
                    <option>Spa Masculino</option>
                </select>
            </div>
            <div class="input-group">
                <label>Hora</label>
                <select id="timeSelect" required>
                    <option value="">Selecciona una hora</option>
                </select>
            </div>
            <div class="input-group full-width">
                <label>Comentarios</label>
                <textarea placeholder="Escribe alguna indicación especial..."></textarea>
            </div>
            <button type="submit" class="booking-btn">Confirmar Reserva</button>
        </form>
    </div>
</div>

<script>
const timeSelect = document.getElementById("timeSelect");
function generateTimes(){
    let startHour = 9; let endHour = 14;
    for(let hour = startHour; hour <= endHour; hour++){
        ["00", "30"].forEach(min => {
            if(hour === endHour && min === "30") return;
            let option = document.createElement("option");
            let displayHour = hour > 12 ? hour - 12 : hour;
            let ampm = hour >= 12 ? "PM" : "AM";
            option.textContent = `${displayHour}:${min} ${ampm}`;
            option.value = `${displayHour}:${min} ${ampm}`;
            timeSelect.appendChild(option);
        });
    }
}
generateTimes();

const monthYear = document.getElementById("monthYear");
const calendarDates = document.getElementById("calendarDates");
const prevMonth = document.getElementById("prevMonth");
const nextMonth = document.getElementById("nextMonth");
let currentDate = new Date();

function renderCalendar(){
    calendarDates.innerHTML = "";
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const firstDay = new Date(year, month, 1).getDay();
    const lastDate = new Date(year, month + 1, 0).getDate();
    const months = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
    monthYear.textContent = `${months[month]} ${year}`;
    for(let i = 0; i < firstDay; i++){
        calendarDates.appendChild(document.createElement("div"));
    }
    for(let day = 1; day <= lastDate; day++){
        const date = document.createElement("div");
        date.classList.add("date");
        date.textContent = day;
        date.addEventListener("click", () => {
            document.querySelectorAll(".date").forEach(d => d.classList.remove("active"));
            date.classList.add("active");
        });
        calendarDates.appendChild(date);
    }
}
prevMonth.addEventListener("click", () => { currentDate.setMonth(currentDate.getMonth() - 1); renderCalendar(); });
nextMonth.addEventListener("click", () => { currentDate.setMonth(currentDate.getMonth() + 1); renderCalendar(); });
renderCalendar();

const chairSlides = document.querySelectorAll(".chair-slide");
const prevChair = document.getElementById("prevChair");
const nextChair = document.getElementById("nextChair");
const selectedChair = document.getElementById("selectedChair");
let currentChair = 0;

function showChair(index){
    chairSlides.forEach(slide => slide.classList.remove("active-chair"));
    chairSlides[index].classList.add("active-chair");
    selectedChair.textContent = chairSlides[index].textContent.trim();
}
nextChair.addEventListener("click", () => { currentChair++; if(currentChair >= chairSlides.length) currentChair = 0; showChair(currentChair); });
prevChair.addEventListener("click", () => { currentChair--; if(currentChair < 0) currentChair = chairSlides.length - 1; showChair(currentChair); });
showChair(currentChair);

document.addEventListener("DOMContentLoaded", () => {
    const bookingModal = document.getElementById("bookingModal");
    const openBookingModal = document.getElementById("openBookingModal");
    const closeModal = document.getElementById("closeModal");
    openBookingModal.addEventListener("click", () => bookingModal.classList.add("active"));
    closeModal.addEventListener("click", () => bookingModal.classList.remove("active"));
    bookingModal.addEventListener("click", (e) => { if(e.target === bookingModal) bookingModal.classList.remove("active"); });
});
</script>
</body>
</html>