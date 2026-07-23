<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Machin Barber — Corte, barba y oficio</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,500;0,9..144,600;1,9..144,400;1,9..144,500&family=Manrope:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#15110c;
    --bg-2:#1b150e;
    --surface:#211a12;
    --surface-2:#271f15;
    --ink:#f3ead9;
    --ink-dim:#cdbfa8;
    --muted:#9c8b74;
    --brass:#c1904f;
    --brass-bright:#d9a862;
    --wine:#77232c;
    --wine-bright:#8f2c34;
    --cream:#e9ddc4;
    --ok:#5fbf83;
    --error:#e2685a;
    --line: rgba(243,234,217,0.14);
    --shadow: 0 30px 60px -20px rgba(0,0,0,0.6);
  }

  *{margin:0;padding:0;box-sizing:border-box;}
  html{scroll-behavior:smooth;}

  body{
    background:var(--bg);
    color:var(--ink);
    font-family:'Manrope', sans-serif;
    font-size:16px;
    line-height:1.6;
    overflow-x:hidden;
  }

  ::selection{background:var(--brass); color:#15110c;}

  h1,h2,h3,h4{
    font-family:'Fraunces', serif;
    font-weight:500;
    line-height:1.05;
    letter-spacing:-0.01em;
  }

  .mono{
    font-family:'IBM Plex Mono', monospace;
    letter-spacing:0.06em;
    text-transform:uppercase;
  }

  a{color:inherit; text-decoration:none;}
  img{display:block; width:100%; height:100%; object-fit:cover;}

  .wrap{
    max-width:1240px;
    margin:0 auto;
    padding:0 40px;
  }

  /* ===== nav ===== */
  header{
    position:fixed;
    top:0; left:0; right:0;
    z-index:400;
    padding:22px 0;
    transition: background .4s ease, padding .4s ease, border-color .4s ease;
    border-bottom:1px solid transparent;
  }
  header.scrolled{
    background:rgba(21,17,12,0.88);
    backdrop-filter: blur(10px);
    padding:14px 0;
    border-color: var(--line);
  }
  nav.wrap{
    display:flex;
    align-items:center;
    justify-content:space-between;
  }
  .logo{
    font-family:'Fraunces', serif;
    font-size:22px;
    font-weight:600;
    letter-spacing:0.02em;
    display:flex;
    align-items:center;
    gap:10px;
  }
  .logo .mark{
    width:34px; height:34px;
    border:1px solid var(--brass);
    border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:14px;
    color:var(--brass-bright);
    flex-shrink:0;
  }
  .logo span{ color:var(--brass); font-style:italic; font-weight:500;}
  .nav-links{
    display:flex;
    gap:34px;
    list-style:none;
  }
  .nav-links a{
    font-size:13px;
    letter-spacing:0.08em;
    text-transform:uppercase;
    color:var(--ink-dim);
    font-weight:500;
    position:relative;
    padding-bottom:4px;
  }
  .nav-links a::after{
    content:"";
    position:absolute; left:0; bottom:0;
    width:0; height:1px;
    background:var(--brass);
    transition:width .3s ease;
  }
  .nav-links a:hover::after{width:100%;}
  .nav-links a:hover{color:var(--ink);}

  .btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:13px 26px;
    border-radius:2px;
    font-size:12.5px;
    letter-spacing:0.1em;
    text-transform:uppercase;
    font-weight:600;
    font-family:'IBM Plex Mono', monospace;
    cursor:pointer;
    border:1px solid var(--brass);
    transition: all .3s ease;
  }
  .btn-solid{
    background:var(--brass);
    color:#15110c;
  }
  .btn-solid:hover{ background:var(--brass-bright); transform:translateY(-2px); box-shadow:0 10px 24px -8px rgba(193,144,79,0.5);}
  .btn-ghost{
    background:transparent;
    color:var(--ink);
    border-color:var(--line);
  }
  .btn-ghost:hover{border-color:var(--brass); color:var(--brass-bright);}

  .nav-cta{display:flex; align-items:center; gap:18px;}
  .burger{display:none;}

  /* ===== hero ===== */
  .hero{
    position:relative;
    height:100svh;
    min-height:640px;
    display:flex;
    align-items:flex-end;
    overflow:hidden;
  }
  .hero-slides{ position:absolute; inset:0; }
  .hero-slide{ position:absolute; inset:0; opacity:0; transition:opacity 1.6s ease; }
  .hero-slide.active{opacity:1;}
  .hero-slide img{
    width:100%; height:100%; object-fit:cover;
    filter: grayscale(15%) brightness(0.62) contrast(1.08);
    transform:scale(1.06);
  }
  .hero::after{
    content:"";
    position:absolute; inset:0;
    background:
      linear-gradient(180deg, rgba(21,17,12,0.35) 0%, rgba(21,17,12,0.15) 30%, rgba(21,17,12,0.55) 68%, rgba(21,17,12,0.96) 100%),
      linear-gradient(90deg, rgba(21,17,12,0.5) 0%, transparent 45%);
    z-index:1;
  }
  .hero-content{
    position:relative;
    z-index:2;
    padding-bottom:110px;
    padding-left:40px;
    padding-right:40px;
    max-width:1240px;
    margin:0 auto;
    width:100%;
  }
  .hero-eyebrow{
    font-size:12px;
    color:var(--brass-bright);
    margin-bottom:22px;
    display:flex;
    align-items:center;
    gap:14px;
  }
  .hero-eyebrow::before{ content:""; width:34px; height:1px; background:var(--brass-bright); }
  .hero h1{
    font-size:clamp(46px, 7.4vw, 104px);
    max-width:900px;
    color:var(--cream);
  }
  .hero h1 em{ font-style:italic; color:var(--brass-bright); font-weight:400; }
  .hero-sub{
    margin-top:26px;
    max-width:460px;
    color:var(--ink-dim);
    font-size:16.5px;
  }
  .hero-actions{
    margin-top:40px;
    display:flex;
    align-items:center;
    gap:26px;
    flex-wrap:wrap;
  }
  .hero-phone{
    font-family:'IBM Plex Mono', monospace;
    font-size:13.5px;
    letter-spacing:0.05em;
    color:var(--ink-dim);
    border-bottom:1px solid var(--line);
    padding-bottom:3px;
  }
  .hero-phone:hover{color:var(--brass-bright); border-color:var(--brass-bright);}

  .hero-dots{
    position:absolute; right:40px; bottom:110px; z-index:3;
    display:flex; flex-direction:column; gap:11px;
  }
  .hero-dot{
    width:8px; height:8px; border-radius:50%;
    border:1px solid var(--ink-dim); background:transparent;
    cursor:pointer; padding:0; transition:.3s ease;
  }
  .hero-dot.active{ background:var(--brass-bright); border-color:var(--brass-bright); transform:scale(1.3);}

  .scroll-cue{
    position:absolute; left:40px; bottom:36px; z-index:3;
    display:flex; align-items:center; gap:10px;
    font-family:'IBM Plex Mono', monospace;
    font-size:11px; letter-spacing:0.14em; color:var(--ink-dim); text-transform:uppercase;
  }
  .scroll-cue::after{
    content:""; width:1px; height:28px;
    background:linear-gradient(var(--brass-bright), transparent);
    animation: scrollLine 2.2s ease-in-out infinite;
  }
  @keyframes scrollLine{
    0%{ transform:scaleY(0); transform-origin:top;}
    50%{ transform:scaleY(1); transform-origin:top;}
    50.01%{transform-origin:bottom;}
    100%{transform:scaleY(0); transform-origin:bottom;}
  }

  /* ===== marquee strip ===== */
  .marquee{
    background:var(--wine);
    border-top:1px solid rgba(0,0,0,0.2);
    border-bottom:1px solid rgba(0,0,0,0.2);
    overflow:hidden;
    padding:16px 0;
  }
  .marquee-track{ display:flex; width:max-content; animation: marquee 30s linear infinite; }
  .marquee-track span{
    font-family:'IBM Plex Mono', monospace;
    font-size:14px; letter-spacing:0.14em; text-transform:uppercase;
    color:var(--cream); padding:0 28px; white-space:nowrap;
    display:flex; align-items:center; gap:28px;
  }
  .marquee-track span::after{ content:"—"; color:var(--brass-bright); }
  @keyframes marquee{ from{transform:translateX(0);} to{transform:translateX(-50%);} }

  /* ===== section basics ===== */
  section{ padding:120px 0; position:relative;}
  @media(max-width:720px){ section{padding:80px 0;} }

  .eyebrow{
    font-family:'IBM Plex Mono', monospace;
    font-size:12px; letter-spacing:0.18em; text-transform:uppercase;
    color:var(--brass-bright);
    display:flex; align-items:center; gap:14px;
    margin-bottom:20px;
  }
  .eyebrow::before{ content:""; width:28px; height:1px; background:var(--brass-bright); }

  .reveal{
    opacity:0;
    transform:translateY(28px);
    transition: opacity .9s cubic-bezier(.16,.84,.44,1), transform .9s cubic-bezier(.16,.84,.44,1);
  }
  .reveal.in{ opacity:1; transform:none; }

  /* ===== about ===== */
  .about{ display:grid; grid-template-columns: 0.9fr 1.1fr; gap:80px; align-items:center; }
  .about-media{ position:relative; aspect-ratio:4/5; overflow:hidden; border-radius:2px; }
  .about-media img{ filter:grayscale(20%) contrast(1.05); }
  .about-media::before{
    content:""; position:absolute; inset:0;
    border:1px solid var(--brass);
    outline:1px solid rgba(21,17,12,0.4);
    outline-offset:-16px;
    z-index:2; pointer-events:none;
  }
  .about h2{ font-size:clamp(32px, 3.6vw, 48px); max-width:520px; }
  .about h2 em{ font-style:italic; color:var(--brass-bright); font-weight:400;}
  .about p{ margin-top:26px; color:var(--ink-dim); max-width:480px; font-size:16px; }
  .pull-quote{
    margin-top:40px; padding-left:26px; border-left:2px solid var(--brass);
    font-family:'Fraunces', serif; font-style:italic; font-size:22px; color:var(--cream); max-width:460px;
  }
  .about-stats{ margin-top:44px; display:flex; gap:48px; }
  .about-stats div strong{ display:block; font-family:'Fraunces', serif; font-size:34px; color:var(--brass-bright); font-weight:500; }
  .about-stats div span{ font-family:'IBM Plex Mono', monospace; font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:var(--muted); }

  @media(max-width:900px){ .about{ grid-template-columns:1fr; gap:44px; } }

  /* ===== services — ticket style ===== */
  .services{ background:var(--surface); }
  .services-head{ display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:24px; margin-bottom:64px; }
  .services-head h2{ font-size:clamp(32px,3.6vw,48px); }
  .services-head h2 em{font-style:italic; color:var(--brass-bright); font-weight:400;}
  .services-note{ max-width:340px; color:var(--muted); font-size:14px; }

  .ticket{ background:var(--surface-2); border:1px solid var(--line); padding:52px; position:relative; }
  .ticket::before, .ticket::after{
    content:""; position:absolute; left:0; right:0; height:14px;
    background: radial-gradient(circle at 12px 50%, var(--bg) 7px, transparent 7.5px) repeat-x;
    background-size:24px 14px;
  }
  .ticket::before{ top:-7px; }
  .ticket::after{ bottom:-7px; transform:scaleY(-1);}

  .ticket-grid{ display:grid; grid-template-columns:1fr 1fr; gap:0 60px; }
  .ticket-group{ margin-bottom:38px; }
  .ticket-group h4{
    font-family:'IBM Plex Mono', monospace; font-size:12px; letter-spacing:0.14em; text-transform:uppercase;
    color:var(--brass-bright); margin-bottom:20px; padding-bottom:12px; border-bottom:1px dashed var(--line);
  }
  .service-row{ display:flex; align-items:baseline; gap:10px; padding:10px 0; }
  .service-row .name{ font-family:'Fraunces', serif; font-size:17.5px; color:var(--ink); white-space:nowrap; }
  .service-row .leader{ flex:1; border-bottom:1px dotted var(--muted); transform:translateY(-4px); opacity:0.5; }
  .service-row .price{ font-family:'IBM Plex Mono', monospace; font-size:15px; color:var(--brass-bright); white-space:nowrap; }
  .service-row .sub{ display:block; font-size:12.5px; color:var(--muted); font-family:'Manrope'; margin-top:2px; }

  @media(max-width:760px){ .ticket{padding:32px 24px;} .ticket-grid{ grid-template-columns:1fr; } }

  /* ===== gallery carousel ===== */
  .gallery-head{ display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:40px; flex-wrap:wrap; gap:20px; }
  .gallery-head h2{ font-size:clamp(32px,3.6vw,48px); }
  .gallery-head h2 em{font-style:italic; color:var(--brass-bright); font-weight:400;}
  .gallery-controls{ display:flex; gap:10px; }
  .g-btn{
    width:46px; height:46px; border:1px solid var(--line); background:transparent; color:var(--ink);
    display:flex; align-items:center; justify-content:center; cursor:pointer; transition:.25s ease;
  }
  .g-btn:hover{ border-color:var(--brass); color:var(--brass-bright); }

  .gallery-track{ display:flex; gap:20px; overflow-x:auto; scroll-snap-type:x mandatory; scrollbar-width:none; padding-bottom:6px; }
  .gallery-track::-webkit-scrollbar{display:none;}
  .g-item{ scroll-snap-align:start; flex:0 0 auto; width:340px; height:440px; position:relative; overflow:hidden; }
  .g-item:nth-child(3n+2){ width:280px; height:380px; align-self:flex-end; }
  .g-item img{
    filter:grayscale(25%) contrast(1.05);
    transition: transform 1.1s cubic-bezier(.16,.84,.44,1), filter .5s ease;
  }
  .g-item:hover img{ transform:scale(1.08); filter:grayscale(0%) contrast(1.08); }
  .g-cap{
    position:absolute; left:0; right:0; bottom:0; padding:18px 20px;
    background:linear-gradient(0deg, rgba(0,0,0,0.75), transparent);
    font-family:'IBM Plex Mono', monospace; font-size:11.5px; letter-spacing:0.08em; text-transform:uppercase;
    color:var(--cream); opacity:0; transform:translateY(8px); transition:.35s ease;
  }
  .g-item:hover .g-cap{ opacity:1; transform:none; }

  @media(max-width:600px){
    .g-item{ width:78vw; height:60vw; }
    .g-item:nth-child(3n+2){ width:78vw; height:60vw; align-self:auto; }
  }

  /* ===== team ===== */
  .team{ background:var(--surface); }
  .team-head{ max-width:560px; margin-bottom:64px; }
  .team-head h2{ font-size:clamp(32px,3.6vw,48px); }
  .team-head h2 em{font-style:italic; color:var(--brass-bright); font-weight:400;}
  .team-head p{ margin-top:18px; color:var(--muted); }

  .team-grid{ display:grid; grid-template-columns:repeat(4,1fr); gap:1px; background:var(--line); border:1px solid var(--line); }
  .barber-card{ background:var(--surface); padding:38px 28px; transition: background .35s ease; }
  .barber-card:hover{ background:var(--surface-2); }
  .barber-avatar{
    width:60px; height:60px; border-radius:50%; border:1px solid var(--brass);
    display:flex; align-items:center; justify-content:center;
    font-family:'Fraunces', serif; font-style:italic; font-size:20px; color:var(--brass-bright);
    margin-bottom:24px;
  }
  .barber-card h3{ font-size:20px; font-weight:500; }
  .barber-role{
    font-family:'IBM Plex Mono', monospace; font-size:11px; letter-spacing:0.08em; text-transform:uppercase;
    color:var(--brass-bright); margin-top:6px;
  }
  .barber-card p{ margin-top:16px; color:var(--muted); font-size:14px; }
  .barber-tags{ margin-top:20px; display:flex; gap:7px; flex-wrap:wrap; }
  .barber-tags span{
    font-family:'IBM Plex Mono', monospace; font-size:10px; letter-spacing:0.05em; text-transform:uppercase;
    padding:5px 9px; border:1px solid var(--line); color:var(--ink-dim);
  }

  @media(max-width:980px){ .team-grid{ grid-template-columns:repeat(2,1fr); } }
  @media(max-width:560px){ .team-grid{ grid-template-columns:1fr; } }

  /* ===== testimonials ===== */
  .reviews-grid{ display:grid; grid-template-columns:repeat(3,1fr); gap:44px; }
  .review-card{ padding-top:26px; border-top:1px solid var(--line); }
  .review-stars{ color:var(--brass-bright); font-size:13px; letter-spacing:2px; margin-bottom:18px; }
  .review-card p{ font-family:'Fraunces', serif; font-size:19px; line-height:1.5; color:var(--cream); }
  .review-name{ margin-top:22px; font-family:'IBM Plex Mono', monospace; font-size:12px; letter-spacing:0.08em; text-transform:uppercase; color:var(--muted); }
  @media(max-width:820px){ .reviews-grid{ grid-template-columns:1fr; gap:36px; } }

  /* ===== booking / calendario ===== */
  .booking{ background:var(--surface); }
  .booking-head{ display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:24px; margin-bottom:56px; }
  .booking-head h2{ font-size:clamp(32px,3.6vw,48px); }
  .booking-head h2 em{ font-style:italic; color:var(--brass-bright); font-weight:400; }
  .booking-note{ max-width:340px; color:var(--muted); font-size:14px; }

  .booking-panel{ max-width:820px; margin:0 auto; }

  .chairs-title{
    font-family:'IBM Plex Mono', monospace; font-size:12px; letter-spacing:0.14em; text-transform:uppercase;
    color:var(--brass-bright); margin-bottom:22px; padding-bottom:12px; border-bottom:1px dashed var(--line);
  }
  .chairs-slider-container{ display:flex; justify-content:center; align-items:center; gap:20px; }
  .chair-arrow{
    width:50px; height:50px; border:1px solid var(--line); border-radius:2px; background:transparent;
    color:var(--ink); font-size:20px; cursor:pointer; transition:.25s ease;
  }
  .chair-arrow:hover{ border-color:var(--brass); color:var(--brass-bright); }
  .chair-slide-wrapper{ width:280px; }
  .chair-slide{
    display:none; width:100%; height:130px; border:1px solid var(--line);
    align-items:center; justify-content:center; flex-direction:column; gap:8px;
    color:var(--brass-bright); font-family:'Fraunces', serif;
  }
  .chair-slide .icon{ font-size:24px; }
  .chair-slide .label{ font-size:19px; font-weight:500; }
  .active-chair{ display:flex; }
  .selected-chair-text{ text-align:center; margin-top:20px; color:var(--muted); font-size:14px; }
  .selected-chair-text span{ color:var(--brass-bright); font-weight:600; }

  .calendar-section{ margin-top:50px; }
  .calendar-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
  .calendar-header h3{ color:var(--brass-bright); font-family:'IBM Plex Mono', monospace; font-size:14px; letter-spacing:0.06em; text-transform:uppercase; font-weight:500;}
  .calendar-header button{
    width:40px; height:40px; border:1px solid var(--line); background:transparent; color:var(--ink);
    cursor:pointer; transition:.25s ease;
  }
  .calendar-header button:hover{ border-color:var(--brass); color:var(--brass-bright); }
  .calendar-days, .calendar-dates{ display:grid; grid-template-columns:repeat(7,1fr); gap:8px; }
  .calendar-days div{ text-align:center; color:var(--muted); font-family:'IBM Plex Mono', monospace; font-size:11px; letter-spacing:0.06em; text-transform:uppercase; padding-bottom:6px; }
  .date{
    height:58px; display:flex; justify-content:center; align-items:center;
    border:1px solid var(--line); cursor:pointer; font-size:14px; color:var(--ink-dim); transition:.2s ease;
  }
  .date:hover{ border-color:var(--brass); color:var(--ink); }
  .date.active{ background:var(--brass); color:#15110c; font-weight:700; border-color:var(--brass); }
  .date.disabled{ opacity:0.25; pointer-events:none; }

  .reserve-button-container{ display:flex; justify-content:center; margin-top:46px; }

  /* ===== modal wizard ===== */
  .booking-modal{
    position:fixed; inset:0; background:rgba(0,0,0,0.75); backdrop-filter:blur(4px);
    display:flex; justify-content:center; align-items:center;
    opacity:0; visibility:hidden; transition:.3s ease; z-index:800; padding:20px;
  }
  .booking-modal.active{ opacity:1; visibility:visible; }
  .modal-content{
    width:100%; max-width:600px; max-height:88vh; overflow-y:auto;
    background:var(--surface-2); border:1px solid var(--line); padding:44px; position:relative;
    transform:translateY(30px) scale(0.97); transition:transform .35s cubic-bezier(.16,.84,.44,1);
  }
  .booking-modal.active .modal-content{ transform:none; }
  .close-modal{
    position:absolute; top:20px; right:20px; width:40px; height:40px; border:1px solid var(--line);
    background:transparent; color:var(--ink); font-size:16px; cursor:pointer; transition:.25s ease;
  }
  .close-modal:hover{ border-color:var(--brass); color:var(--brass-bright); }
  .modal-title{ text-align:center; color:var(--cream); font-size:28px; margin-bottom:8px; }
  .modal-sub{ text-align:center; color:var(--muted); font-family:'IBM Plex Mono', monospace; font-size:12px; letter-spacing:0.08em; text-transform:uppercase; margin-bottom:30px; }

  .steps-bar{ display:flex; gap:8px; margin-bottom:34px; }
  .steps-bar .bar{ flex:1; height:2px; background:var(--line); overflow:hidden; }
  .steps-bar .bar span{ display:block; height:100%; width:0%; background:var(--brass); transition:width .4s ease; }
  .steps-bar .bar.done span{ width:100%; }
  .steps-bar .bar.current span{ width:60%; }

  .wizard-step{ display:none; }
  .wizard-step.active{ display:block; animation:stepIn .45s ease; }
  @keyframes stepIn{ from{ opacity:0; transform:translateX(14px);} to{ opacity:1; transform:none;} }

  .booking-form{ display:grid; grid-template-columns:1fr 1fr; gap:22px; }
  .input-group{ display:flex; flex-direction:column; }
  .input-group.full-width{ grid-column:span 2; }
  .input-group label{ margin-bottom:9px; color:var(--brass-bright); font-family:'IBM Plex Mono', monospace; font-size:11.5px; letter-spacing:0.06em; text-transform:uppercase; }
  .input-group input, .input-group select, .input-group textarea{
    padding:13px 14px; border:1px solid var(--line); background:var(--bg); color:var(--ink);
    outline:none; font-family:'Manrope', sans-serif; font-size:14.5px; transition:border-color .25s ease;
  }
  .input-group input:focus, .input-group select:focus, .input-group textarea:focus{ border-color:var(--brass); }
  .input-group textarea{ height:100px; resize:none; }

  .summary-box{ background:var(--bg); border:1px solid var(--line); padding:22px; margin-bottom:26px; }
  .summary-row{ display:flex; justify-content:space-between; padding:9px 0; border-bottom:1px dashed var(--line); font-size:14px; }
  .summary-row:last-child{ border-bottom:none; }
  .summary-row span:first-child{ color:var(--muted); font-family:'IBM Plex Mono', monospace; font-size:11.5px; text-transform:uppercase; letter-spacing:0.05em; }
  .summary-row span:last-child{ color:var(--brass-bright); font-weight:500; }

  .wizard-actions{ display:flex; justify-content:space-between; gap:14px; margin-top:30px; }
  .wizard-actions.center{ justify-content:center; }
  .btn-wizard{
    padding:13px 24px; border:1px solid var(--line); background:transparent; color:var(--ink);
    cursor:pointer; font-family:'IBM Plex Mono', monospace; font-size:12.5px; letter-spacing:0.06em; text-transform:uppercase; transition:.25s ease;
  }
  .btn-wizard:hover{ border-color:var(--brass); color:var(--brass-bright); }
  .btn-wizard.primary{ background:var(--brass); color:#15110c; border-color:var(--brass); flex:1; font-weight:600; }
  .btn-wizard.primary:hover{ background:var(--brass-bright); }

  .success-screen{ text-align:center; padding:20px 0; }
  .check-circle{
    width:80px; height:80px; border-radius:50%; border:1px solid var(--ok); margin:0 auto 26px;
    display:flex; align-items:center; justify-content:center;
  }
  .check-circle svg{ width:34px; height:34px; }
  .check-circle path{
    stroke:var(--ok); stroke-width:3; fill:none; stroke-linecap:round; stroke-linejoin:round;
    stroke-dasharray:40; stroke-dashoffset:40; animation:drawCheck .5s ease .2s forwards;
  }
  @keyframes drawCheck{ to{ stroke-dashoffset:0; } }
  .success-screen h3{ color:var(--cream); font-size:24px; margin-bottom:10px; }
  .success-screen p{ color:var(--muted); font-size:14.5px; margin-bottom:26px; }

  @media(max-width:640px){
    .booking-form{ grid-template-columns:1fr; }
    .input-group.full-width{ grid-column:span 1; }
    .modal-content{ padding:30px 22px; }
  }

  /* ===== hours / location ===== */
  .visit{ background:var(--surface); }
  .visit-grid{ display:grid; grid-template-columns:1fr 1fr; gap:80px; }
  .visit h2{ font-size:clamp(30px,3.4vw,44px); margin-bottom:30px;}
  .visit h2 em{font-style:italic; color:var(--brass-bright); font-weight:400;}
  .hours-row{
    display:flex; justify-content:space-between; padding:14px 0; border-bottom:1px dashed var(--line);
    font-family:'IBM Plex Mono', monospace; font-size:14px;
  }
  .hours-row span:first-child{ color:var(--ink-dim); text-transform:uppercase; letter-spacing:0.06em; font-size:12.5px;}
  .hours-row span:last-child{ color:var(--brass-bright); }
  .visit-info{ margin-top:36px; color:var(--muted); font-size:14.5px; line-height:1.9;}
  .visit-info strong{ color:var(--ink); display:block; font-family:'Manrope'; font-weight:600; margin-bottom:4px;}

  .map-box{ position:relative; aspect-ratio:1/1; border:1px solid var(--line); overflow:hidden; }
  .map-box img{ filter:grayscale(70%) brightness(0.5) sepia(15%); }
  .map-pin{ position:absolute; top:50%; left:50%; transform:translate(-50%,-100%); display:flex; flex-direction:column; align-items:center; }
  .map-pin-dot{ width:16px; height:16px; border-radius:50%; background:var(--brass-bright); box-shadow:0 0 0 6px rgba(217,168,98,0.22), 0 0 24px rgba(217,168,98,0.5); }
  .map-pin::after{ content:""; width:1px; height:34px; background:var(--brass-bright); }
  .map-label{
    position:absolute; bottom:20px; left:20px;
    font-family:'IBM Plex Mono', monospace; font-size:11.5px; letter-spacing:0.08em; text-transform:uppercase;
    color:var(--cream); background:rgba(21,17,12,0.7); padding:10px 14px;
  }

  @media(max-width:860px){ .visit-grid{ grid-template-columns:1fr; gap:50px; } }

  /* ===== cta band ===== */
  .cta-band{ background:var(--brass); color:#15110c; padding:90px 0; text-align:center; }
  .cta-band h2{ font-size:clamp(32px,4.4vw,58px); color:#15110c; }
  .cta-band h2 em{ font-style:italic; font-weight:400; }
  .cta-band p{ margin-top:16px; color:rgba(21,17,12,0.72); font-size:16px; }
  .cta-band .hero-actions{ justify-content:center; margin-top:38px; }
  .cta-band .btn-solid{ background:#15110c; color:var(--cream); border-color:#15110c; }
  .cta-band .btn-solid:hover{ background:#241c13; }
  .cta-band .btn-ghost{ border-color:rgba(21,17,12,0.4); color:#15110c; }
  .cta-band .btn-ghost:hover{ border-color:#15110c; }

  /* ===== footer ===== */
  footer{ padding:70px 0 34px; }
  .foot-grid{
    display:grid; grid-template-columns: 1.3fr 1fr 1fr 1fr; gap:50px; padding-bottom:50px; border-bottom:1px solid var(--line);
  }
  .foot-logo{ font-family:'Fraunces', serif; font-size:24px; font-weight:600;}
  .foot-logo span{ color:var(--brass-bright); font-style:italic; font-weight:400;}
  .foot-grid p{ margin-top:16px; color:var(--muted); font-size:14px; max-width:260px; }
  .foot-col h5{
    font-family:'IBM Plex Mono', monospace; font-size:11.5px; letter-spacing:0.1em; text-transform:uppercase;
    color:var(--brass-bright); margin-bottom:18px;
  }
  .foot-col a{ display:block; color:var(--ink-dim); font-size:14.5px; margin-bottom:12px; transition:.25s ease; }
  .foot-col a:hover{ color:var(--brass-bright); }
  .foot-bottom{
    padding-top:26px; display:flex; justify-content:space-between; flex-wrap:wrap; gap:12px;
    font-family:'IBM Plex Mono', monospace; font-size:11.5px; letter-spacing:0.05em; color:var(--muted);
  }
  @media(max-width:820px){ .foot-grid{ grid-template-columns:1fr 1fr; } }
  @media(max-width:560px){ .foot-grid{ grid-template-columns:1fr; } }

  /* ===== mobile nav ===== */
  @media(max-width:900px){
    .nav-links{ display:none; }
    .burger{
      display:flex; width:38px; height:38px; border:1px solid var(--line);
      align-items:center; justify-content:center; cursor:pointer; background:transparent;
    }
    .burger span{ display:block; width:16px; height:1px; background:var(--ink); position:relative; }
    .burger span::before, .burger span::after{ content:""; position:absolute; width:16px; height:1px; background:var(--ink); left:0; }
    .burger span::before{ top:-5px; }
    .burger span::after{ top:5px; }
    .nav-cta .btn-ghost{ display:none; }

    .mobile-menu{
      position:fixed; inset:0; background:var(--bg); z-index:600;
      display:flex; flex-direction:column; padding:110px 40px 40px;
      transform:translateX(100%); transition:transform .45s cubic-bezier(.16,.84,.44,1);
    }
    .mobile-menu.open{ transform:translateX(0); }
    .mobile-menu a{ font-family:'Fraunces', serif; font-size:30px; padding:16px 0; border-bottom:1px solid var(--line); }
    .mobile-close{
      position:absolute; top:26px; right:36px; font-family:'IBM Plex Mono', monospace; color:var(--muted);
      background:none; border:none; cursor:pointer; font-size:13px; letter-spacing:0.1em; text-transform:uppercase;
    }
  }
  @media(min-width:901px){ .mobile-menu{ display:none; } }

  @media (prefers-reduced-motion: reduce){
    *{ animation-duration:0.01ms !important; animation-iteration-count:1 !important; transition-duration:0.01ms !important; scroll-behavior:auto !important;}
  }

  a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible{
    outline:2px solid var(--brass-bright);
    outline-offset:3px;
  }
</style>
</head>
<body>

<header id="siteHeader">
  <nav class="wrap">
    <a href="#inicio" class="logo"><span class="mark">M</span>MACHIN <span>barber</span></a>
    <ul class="nav-links">
      <li><a href="#nosotros">Nosotros</a></li>
      <li><a href="#servicios">Servicios</a></li>
      <li><a href="#galeria">Galería</a></li>
      <li><a href="#equipo">Equipo</a></li>
      <li><a href="#resenas">Reseñas</a></li>
      <li><a href="#visita">Visítanos</a></li>
    </ul>
    <div class="nav-cta">
      <a href="tel:+523398765432" class="btn btn-ghost">33 9876 5432</a>
      <a href="#reservar" class="btn btn-solid">Reservar</a>
      <button class="burger" id="burgerBtn" aria-label="Abrir menú"><span></span></button>
    </div>
  </nav>
</header>

<div class="mobile-menu" id="mobileMenu">
  <button class="mobile-close" id="mobileClose">Cerrar ✕</button>
  <a href="#nosotros">Nosotros</a>
  <a href="#servicios">Servicios</a>
  <a href="#galeria">Galería</a>
  <a href="#equipo">Equipo</a>
  <a href="#resenas">Reseñas</a>
  <a href="#visita">Visítanos</a>
  <a href="#reservar">Reservar cita</a>
</div>

<!-- ===== HERO ===== -->
<section class="hero" id="inicio">
  <div class="hero-slides" id="heroSlides">
    <div class="hero-slide active">
      <img src="https://images.unsplash.com/photo-1517832606299-7ae9b720a186?w=1920&q=80&auto=format&fit=crop" alt="Perfilado de barba en Machin Barber">
    </div>
    <div class="hero-slide">
      <img src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=1920&q=80&auto=format&fit=crop" alt="Cliente sentado en el sillón de la barbería">
    </div>
    <div class="hero-slide">
      <img src="https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?w=1920&q=80&auto=format&fit=crop" alt="Ambiente de Machin Barber">
    </div>
  </div>

  <div class="scroll-cue">Desliza</div>

  <div class="hero-content">
    <p class="hero-eyebrow mono">Guadalajara — desde 2016</p>
    <h1>Estilo que <em>impone</em><br>presencia</h1>
    <p class="hero-sub">Más que un corte: una experiencia premium de navaja, tijera y detalle. Reserva tu silla en segundos.</p>
    <div class="hero-actions">
      <a href="#reservar" class="btn btn-solid">Reservar cita</a>
      <a href="tel:+523398765432" class="hero-phone">Llamar — 33 9876 5432</a>
    </div>
  </div>

  <div class="hero-dots" id="heroDots">
    <button class="hero-dot active" data-i="0" aria-label="Diapositiva 1"></button>
    <button class="hero-dot" data-i="1" aria-label="Diapositiva 2"></button>
    <button class="hero-dot" data-i="2" aria-label="Diapositiva 3"></button>
  </div>
</section>

<!-- ===== MARQUEE ===== -->
<div class="marquee">
  <div class="marquee-track">
    <span>Corte clásico</span>
    <span>Fade moderno</span>
    <span>Barba premium</span>
    <span>Afeitado a navaja</span>
    <span>Spa masculino</span>
    <span>Corte clásico</span>
    <span>Fade moderno</span>
    <span>Barba premium</span>
    <span>Afeitado a navaja</span>
    <span>Spa masculino</span>
  </div>
</div>

<!-- ===== ABOUT ===== -->
<section id="nosotros">
  <div class="wrap about">
    <div class="about-media reveal">
      <img src="https://images.unsplash.com/photo-1622286342621-4bd786c2447c?w=1200&q=80&auto=format&fit=crop" alt="Interior de Machin Barber">
    </div>
    <div class="reveal">
      <p class="eyebrow">Nuestra historia</p>
      <h2>Tradición y <em>estilo</em></h2>
      <p>En Machin Barber combinamos técnicas clásicas con estilo moderno para ofrecer una experiencia premium. Cada corte empieza con una conversación y termina con la seguridad de saber que te fuiste mejor de como llegaste.</p>
      <p class="pull-quote">"Aquí no se corta el pelo. Se afila la manera en que entras a un lugar."</p>
      <div class="about-stats">
        <div><strong>9</strong><span>Años de oficio</span></div>
        <div><strong>8</strong><span>Barberos residentes</span></div>
        <div><strong>+6,000</strong><span>Clientes atendidos</span></div>
      </div>
    </div>
  </div>
</section>

<!-- ===== SERVICIOS ===== -->
<section id="servicios" class="services">
  <div class="wrap">
    <div class="services-head reveal">
      <div>
        <p class="eyebrow">La carta</p>
        <h2>Servicios <em>y tarifas</em></h2>
      </div>
      <p class="services-note">Precios en pesos mexicanos. Cada servicio incluye toalla caliente y bebida de cortesía.</p>
    </div>

    <div class="ticket reveal">
      <div class="ticket-grid">
        <div>
          <div class="ticket-group">
            <h4>Cabello</h4>
            <div class="service-row">
              <span class="name">Corte clásico<span class="sub">Tijera y máquina, acabado a navaja</span></span>
              <span class="leader"></span>
              <span class="price">$280</span>
            </div>
            <div class="service-row">
              <span class="name">Fade moderno<span class="sub">Degradado de precisión y diseño</span></span>
              <span class="leader"></span>
              <span class="price">$350</span>
            </div>
            <div class="service-row">
              <span class="name">Corte niño<span class="sub">Hasta 12 años</span></span>
              <span class="leader"></span>
              <span class="price">$220</span>
            </div>
          </div>

          <div class="ticket-group">
            <h4>Spa masculino</h4>
            <div class="service-row">
              <span class="name">Spa Premium<span class="sub">Corte + limpieza facial + exfoliación</span></span>
              <span class="leader"></span>
              <span class="price">$450</span>
            </div>
            <div class="service-row">
              <span class="name">Limpieza facial<span class="sub">Vapor, exfoliación y mascarilla</span></span>
              <span class="leader"></span>
              <span class="price">$300</span>
            </div>
          </div>
        </div>

        <div>
          <div class="ticket-group">
            <h4>Barba y afeitado</h4>
            <div class="service-row">
              <span class="name">Barba & Estilo<span class="sub">Perfilado, toalla caliente y aceites premium</span></span>
              <span class="leader"></span>
              <span class="price">$260</span>
            </div>
            <div class="service-row">
              <span class="name">Afeitado clásico<span class="sub">Toalla caliente y navaja</span></span>
              <span class="leader"></span>
              <span class="price">$240</span>
            </div>
            <div class="service-row">
              <span class="name">Perfilado de cejas</span>
              <span class="leader"></span>
              <span class="price">$120</span>
            </div>
          </div>

          <div class="ticket-group">
            <h4>Experiencias</h4>
            <div class="service-row">
              <span class="name">Ritual Machin<span class="sub">Corte + barba + spa premium</span></span>
              <span class="leader"></span>
              <span class="price">$680</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== GALERÍA ===== -->
<section id="galeria">
  <div class="wrap">
    <div class="gallery-head reveal">
      <div>
        <p class="eyebrow">El espacio</p>
        <h2>Dentro de <em>Machin</em></h2>
      </div>
      <div class="gallery-controls">
        <button class="g-btn" id="gPrev" aria-label="Anterior">←</button>
        <button class="g-btn" id="gNext" aria-label="Siguiente">→</button>
      </div>
    </div>
  </div>

  <div class="wrap">
    <div class="gallery-track" id="galleryTrack">
      <div class="g-item">
        <img src="https://images.unsplash.com/photo-1647140655214-e4a2d914971f?w=900&q=80&auto=format&fit=crop" alt="Barbero cortando el cabello con tijeras">
        <div class="g-cap">Corte a tijera</div>
      </div>
      <div class="g-item">
        <img src="https://images.unsplash.com/photo-1536520002442-39764a41e987?w=900&q=80&auto=format&fit=crop" alt="Interior del salón con lámparas colgantes">
        <div class="g-cap">Sala principal</div>
      </div>
      <div class="g-item">
        <img src="https://images.unsplash.com/photo-1517832606299-7ae9b720a186?w=900&q=80&auto=format&fit=crop" alt="Perfilado de barba con tijeras">
        <div class="g-cap">Perfilado de barba</div>
      </div>
      <div class="g-item">
        <img src="https://images.unsplash.com/photo-1592647420148-bfcc177e2117?w=900&q=80&auto=format&fit=crop" alt="Sillón de barbero en cuero rojo y blanco">
        <div class="g-cap">Sillones originales</div>
      </div>
      <div class="g-item">
        <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=900&q=80&auto=format&fit=crop" alt="Spa premium en barbería">
        <div class="g-cap">Spa masculino</div>
      </div>
      <div class="g-item">
        <img src="https://images.unsplash.com/photo-1678356164573-9a534fe43958?w=900&q=80&auto=format&fit=crop" alt="Fachada de la barbería">
        <div class="g-cap">Fachada Machin</div>
      </div>
    </div>
  </div>
</section>

<!-- ===== EQUIPO ===== -->
<section id="equipo" class="team">
  <div class="wrap">
    <div class="team-head reveal">
      <p class="eyebrow">Quién sostiene la navaja</p>
      <h2>El <em>equipo</em></h2>
      <p>Cuatro barberos, cuatro estilos, un mismo estándar: nada sale del sillón hasta que está bien.</p>
    </div>
  </div>

  <div class="wrap">
    <div class="team-grid reveal">
      <div class="barber-card">
        <div class="barber-avatar">C</div>
        <h3>Carlos Machín</h3>
        <p class="barber-role mono">Fundador · Master barber</p>
        <p>Fundador de la casa. Especialista en degradados de precisión y afeitado tradicional.</p>
        <div class="barber-tags"><span>Fades</span><span>Navaja</span></div>
      </div>
      <div class="barber-card">
        <div class="barber-avatar">I</div>
        <h3>Iván Rosas</h3>
        <p class="barber-role mono">Especialista en barba</p>
        <p>Perfeccionista del contorno. Cada barba se diseña según la estructura del rostro.</p>
        <div class="barber-tags"><span>Barbas</span><span>Cejas</span></div>
      </div>
      <div class="barber-card">
        <div class="barber-avatar">M</div>
        <h3>Marco Uribe</h3>
        <p class="barber-role mono">Corte clásico</p>
        <p>El más pedido para cortes ejecutivos. Precisión británica, ritmo tranquilo.</p>
        <div class="barber-tags"><span>Clásico</span><span>Tijera</span></div>
      </div>
      <div class="barber-card">
        <div class="barber-avatar">D</div>
        <h3>Diego Ponce</h3>
        <p class="barber-role mono">Fades modernos</p>
        <p>Referencia local en degradados de alto contraste y diseño de líneas.</p>
        <div class="barber-tags"><span>Fades</span><span>Diseño</span></div>
        
      </div>
      <div class="barber-card">
  <div class="barber-avatar">A</div>
  <h3>Andrés Salcedo</h3>
  <p class="barber-role mono">Afeitado clásico</p>
  <p>Maestro de la navaja y la toalla caliente. Cada afeitado es un ritual sin prisa.</p>
  <div class="barber-tags"><span>Navaja</span><span>Toalla caliente</span></div>
</div>
<div class="barber-card">
  <div class="barber-avatar">L</div>
  <h3>Luis Fernández</h3>
  <p class="barber-role mono">Spa masculino</p>
  <p>Especialista en tratamientos faciales y cuidado de piel. El detalle que se nota.</p>
  <div class="barber-tags"><span>Facial</span><span>Exfoliación</span></div>
</div>
<div class="barber-card">
  <div class="barber-avatar">J</div>
  <h3>Javier Torres</h3>
  <p class="barber-role mono">Diseño de líneas</p>
  <p>Precisión milimétrica en contornos y diseños. El favorito para looks con carácter.</p>
  <div class="barber-tags"><span>Líneas</span><span>Diseño</span></div>
</div>
<div class="barber-card">
  <div class="barber-avatar">R</div>
  <h3>Rodrigo Nuño</h3>
  <p class="barber-role mono">Barbero junior</p>
  <p>La nueva generación de la casa, formado directamente por Carlos Machín.</p>
  <div class="barber-tags"><span>Clásico</span><span>Fades</span></div>
</div>
    </div>
  </div>
  
</section>

<!-- ===== RESEÑAS ===== -->
<section id="resenas">
  <div class="wrap">
    <p class="eyebrow reveal">Lo que dicen</p>
    <div class="reviews-grid">
      <div class="review-card reveal">
        <p class="review-stars">★★★★★</p>
        <p>"Llevo tres años viniendo y jamás he salido sin saber exactamente qué me hicieron y por qué."</p>
        <p class="review-name">— Andrés M.</p>
      </div>
      <div class="review-card reveal">
        <p class="review-stars">★★★★★</p>
        <p>"El sistema de reservas es súper fácil: eliges silla, día y hora en menos de un minuto."</p>
        <p class="review-name">— Iván R.</p>
      </div>
      <div class="review-card reveal">
        <p class="review-stars">★★★★★</p>
        <p>"No es solo el corte, es el lugar: huele bien, suena bien, y nadie te apura."</p>
        <p class="review-name">— Fernando C.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== RESERVA / CALENDARIO ===== -->
<section id="reservar" class="booking">
  <div class="wrap">
    <div class="booking-head reveal">
      <div>
        <p class="eyebrow">Agenda en línea</p>
        <h2>Reserva <em>tu cita</em></h2>
      </div>
      <p class="booking-note">Elige tu silla, tu día y tu hora. Confirmamos al instante.</p>
    </div>

    <div class="ticket booking-panel reveal">
      <h3 class="chairs-title">Selecciona tu silla</h3>
      <div class="chairs-slider-container">
        <button class="chair-arrow" id="prevChair" aria-label="Silla anterior">❮</button>
        <div class="chair-slide-wrapper">
          <div class="chair-slide active-chair"><span class="icon">💈</span><span class="label">Silla 1</span></div>
          <div class="chair-slide"><span class="icon">💈</span><span class="label">Silla 2</span></div>
          <div class="chair-slide"><span class="icon">💈</span><span class="label">Silla 3</span></div>
          <div class="chair-slide"><span class="icon">💈</span><span class="label">Silla 4</span></div>
          <div class="chair-slide"><span class="icon">💈</span><span class="label">Silla 5</span></div>
          <div class="chair-slide"><span class="icon">💈</span><span class="label">Silla 6</span></div>
        </div>
        <button class="chair-arrow" id="nextChair" aria-label="Silla siguiente">❯</button>
      </div>
      <p class="selected-chair-text">Silla seleccionada: <span id="selectedChair">Silla 1</span></p>

      <div class="calendar-section">
        <div class="calendar-header">
          <button id="prevMonth" aria-label="Mes anterior">❮</button>
          <h3 id="monthYear"></h3>
          <button id="nextMonth" aria-label="Mes siguiente">❯</button>
        </div>
        <div class="calendar-days">
          <div>Dom</div><div>Lun</div><div>Mar</div><div>Mié</div><div>Jue</div><div>Vie</div><div>Sáb</div>
        </div>
        <div class="calendar-dates" id="calendarDates"></div>
      </div>

      <div class="reserve-button-container">
        <button type="button" id="openBookingModal" class="btn btn-solid">Continuar reserva</button>
      </div>
    </div>
  </div>
</section>

<!-- ===== VISITA ===== -->
<section id="visita" class="visit">
  <div class="wrap visit-grid">
    <div class="reveal">
      <p class="eyebrow">Horario y ubicación</p>
      <h2>Visítanos <em>esta semana</em></h2>
      <div class="hours-list">
        <div class="hours-row"><span>Lunes — Viernes</span><span>08:00 – 20:00</span></div>
        <div class="hours-row"><span>Sábado</span><span>08:00 – 20:00</span></div>
        <div class="hours-row"><span>Domingo</span><span>Cerrado</span></div>
      </div>
      <div class="visit-info">
        <strong>Dirección</strong>
        Av. Vallarta 1200, Col. Americana, Guadalajara, Jalisco
        <br><br>
        <strong>Contacto</strong>
        33 9876 5432 · agente@machinbarber.mx
      </div>
    </div>
    <div class="map-box reveal">
      <img src="Captura de pantalla 2026-07-16 165738.png" alt="Mapa de ubicación de Machin Barber">
  </div>
</section>

<!-- ===== CTA ===== -->
<section class="cta-band">
  <div class="wrap">
    <p class="eyebrow" style="justify-content:center; color:#15110c;"><span style="border-color:#15110c"></span>Tu lugar te espera</p>
    <h2>Aparta tu <em>silla</em>.</h2>
    <p>Respondemos en minutos por WhatsApp y llamada. Sin filas, sin esperas de última hora.</p>
    <div class="hero-actions">
      <a href="#reservar" class="btn btn-solid">Reservar en línea</a>
      <a href="https://wa.me/523398765432" class="btn btn-ghost" target="_blank" rel="noopener">WhatsApp</a>
    </div>
  </div>
</section>

<!-- ===== FOOTER ===== -->
<footer>
  <div class="wrap">
    <div class="foot-grid">
      <div>
        <p class="foot-logo">MACHIN <span>barber</span></p>
        <p>Tradición y estilo desde 2016 en el corazón de Guadalajara. Corte, barba y ritual — sin prisas.</p>
      </div>
      <div class="foot-col">
        <h5>Navegación</h5>
        <a href="#nosotros">Nosotros</a>
        <a href="#servicios">Servicios</a>
        <a href="#galeria">Galería</a>
        <a href="#equipo">Equipo</a>
      </div>
      <div class="foot-col">
        <h5>Contacto</h5>
        <a href="tel:+523398765432">33 9876 5432</a>
        <a href="mailto:hola@machinbarber.mx">hola@machinbarber.mx</a>
        <a href="#visita">Col. Americana, GDL</a>
      </div>
      <div class="foot-col">
        <h5>Síguenos</h5>
        <a href="#">Instagram</a>
        <a href="#">Facebook</a>
        <a href="#">TikTok</a>
      </div>
    </div>
    <div class="foot-bottom">
      <span>© 2026 Machin Barber. Todos los derechos reservados.</span>
      <span>Guadalajara, Jalisco, México</span>
    </div>
  </div>
</footer>

<!-- ===== MODAL WIZARD DE RESERVA ===== -->
<div class="booking-modal" id="bookingModal">
  <div class="modal-content">
    <button type="button" class="close-modal" id="closeModal" aria-label="Cerrar">✕</button>
    <h2 class="modal-title">Completa tu reserva</h2>
    <p class="modal-sub" id="modalSub">Paso 1 de 3 — Tus datos</p>

    <div class="steps-bar">
      <div class="bar current" id="bar1"><span></span></div>
      <div class="bar" id="bar2"><span></span></div>
      <div class="bar" id="bar3"><span></span></div>
    </div>

    <form id="bookingForm">
      <!-- PASO 1 -->
      <div class="wizard-step active" data-step="1">
        <div class="booking-form">
          <div class="input-group full-width">
            <label for="nombre">Nombre completo</label>
            <input type="text" id="nombre" placeholder="Ingresa tu nombre" required>
          </div>
          <div class="input-group">
            <label for="telefono">Teléfono</label>
            <input type="tel" id="telefono" placeholder="33 0000 0000" required>
          </div>
          <div class="input-group">
            <label for="servicio">Servicio</label>
            <select id="servicio" required>
              <option value="">Selecciona un servicio</option>
              <option>Corte clásico — $280</option>
              <option>Fade moderno — $350</option>
              <option>Barba & Estilo — $260</option>
              <option>Spa Premium — $450</option>
              <option>Ritual Machin — $680</option>
            </select>
          </div>
          <div class="input-group full-width">
            <label for="barbero">Barbero</label>
            <select id="barbero" required>
              <option value="">Selecciona un barbero</option>
              <option>Carlos Machín</option>
              <option>Iván Rosas</option>
              <option>Marco Uribe</option>
              <option>Diego Ponce</option>
              <option>Andrés Salcedo</option>
              <option>Luis Fernández</option>
              <option>Javier Torres</option>
              <option>Rodrigo Nuño</option>
            </select>
            <span class="field-hint" id="barberoHint"></span>
          </div>
        </div>
        <div class="wizard-actions">
          <span></span>
          <button type="button" class="btn-wizard primary" data-next="2">Continuar</button>
        </div>
      </div>

      <!-- PASO 2 -->
      <div class="wizard-step" data-step="2">
        <div class="booking-form">
          <div class="input-group">
            <label for="timeSelect">Hora</label>
            <select id="timeSelect" required>
              <option value="">Selecciona una hora</option>
            </select>
          </div>
          <div class="input-group">
            <label>Silla</label>
            <input type="text" id="chairDisplay" disabled>
          </div>
          <div class="input-group full-width">
            <label for="comentarios">Comentarios</label>
            <textarea id="comentarios" placeholder="Escribe alguna indicación especial..."></textarea>
          </div>
        </div>
        <div class="wizard-actions">
          <button type="button" class="btn-wizard" data-back="1">Atrás</button>
          <button type="button" class="btn-wizard primary" data-next="3">Revisar reserva</button>
        </div>
      </div>

      <!-- PASO 3 -->
      <div class="wizard-step" data-step="3">
        <div class="summary-box" id="summaryBox"></div>
        <div class="wizard-actions">
          <button type="button" class="btn-wizard" data-back="2">Atrás</button>
          <button type="submit" class="btn-wizard primary">Confirmar reserva</button>
        </div>
      </div>

      <!-- ÉXITO -->
      <div class="wizard-step" data-step="4">
        <div class="success-screen">
          <div class="check-circle">
            <svg viewBox="0 0 24 24"><path d="M4 12.5l5 5L20 6"/></svg>
          </div>
          <h3>¡Reserva confirmada!</h3>
          <p>Te esperamos en Machin Barber. Enviamos la confirmación a tu teléfono.</p>
          <div class="wizard-actions center">
            <button type="button" class="btn-wizard primary" id="finishBooking" style="flex:none; padding:13px 36px;">Listo</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>


<script>
  // header scroll state
  const header = document.getElementById('siteHeader');
  window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 40);
  });

  // mobile menu
  const burger = document.getElementById('burgerBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  const mobileClose = document.getElementById('mobileClose');
  burger.addEventListener('click', () => mobileMenu.classList.add('open'));
  mobileClose.addEventListener('click', () => mobileMenu.classList.remove('open'));
  mobileMenu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => mobileMenu.classList.remove('open')));

  // hero carousel
  const slides = document.querySelectorAll('.hero-slide');
  const heroDotEls = document.querySelectorAll('.hero-dot');
  let current = 0;
  let heroTimer;

  function goToSlide(i){
    slides[current].classList.remove('active');
    heroDotEls[current].classList.remove('active');
    current = i;
    slides[current].classList.add('active');
    heroDotEls[current].classList.add('active');
  }
  function nextSlide(){ goToSlide((current + 1) % slides.length); }
  function startHero(){ heroTimer = setInterval(nextSlide, 5500); }
  startHero();

  heroDotEls.forEach(dot => {
    dot.addEventListener('click', () => {
      clearInterval(heroTimer);
      goToSlide(parseInt(dot.dataset.i));
      startHero();
    });
  });

  // gallery carousel
  const galleryTrack = document.getElementById('galleryTrack');
  document.getElementById('gNext').addEventListener('click', () => galleryTrack.scrollBy({left: 360, behavior:'smooth'}));
  document.getElementById('gPrev').addEventListener('click', () => galleryTrack.scrollBy({left: -360, behavior:'smooth'}));

  // scroll reveal
  const revealEls = document.querySelectorAll('.reveal');
  const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if(entry.isIntersecting){
        entry.target.classList.add('in');
        io.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });
  revealEls.forEach(el => io.observe(el));

  // smooth anchor offset for fixed header
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const id = a.getAttribute('href');
      if(id.length > 1){
        const target = document.querySelector(id);
        if(target){
          e.preventDefault();
          const y = target.getBoundingClientRect().top + window.scrollY - 70;
          window.scrollTo({top: y, behavior:'smooth'});
        }
      }
    });
  });

  /* ================= HORARIOS ================= */
  const timeSelect = document.getElementById("timeSelect");
  function generateTimes(){
    let startHour = 9, endHour = 14;
    for (let hour = startHour; hour <= endHour; hour++){
      ["00", "30"].forEach(min => {
        if (hour === endHour && min === "30") return;
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

  /* ================= CALENDARIO ================= */
  const monthYear = document.getElementById("monthYear");
  const calendarDates = document.getElementById("calendarDates");
  const prevMonthBtn = document.getElementById("prevMonth");
  const nextMonthBtn = document.getElementById("nextMonth");

  let currentDate = new Date();
  let selectedDateText = "";
  const today = new Date(); today.setHours(0,0,0,0);

  function renderCalendar(){
    calendarDates.innerHTML = "";
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const firstDay = new Date(year, month, 1).getDay();
    const lastDate = new Date(year, month + 1, 0).getDate();
    const months = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
    monthYear.textContent = `${months[month]} ${year}`;

    for (let i = 0; i < firstDay; i++){
      calendarDates.appendChild(document.createElement("div"));
    }

    for (let day = 1; day <= lastDate; day++){
      const dateEl = document.createElement("div");
      dateEl.classList.add("date");
      dateEl.textContent = day;

      const thisDate = new Date(year, month, day);
      if (thisDate < today){
        dateEl.classList.add("disabled");
      } else {
        dateEl.addEventListener("click", () => {
          document.querySelectorAll(".date").forEach(d => d.classList.remove("active"));
          dateEl.classList.add("active");
          selectedDateText = `${day} de ${months[month]} ${year}`;
        });
      }
      calendarDates.appendChild(dateEl);
    }
  }

  prevMonthBtn.addEventListener("click", () => { currentDate.setMonth(currentDate.getMonth() - 1); renderCalendar(); });
  nextMonthBtn.addEventListener("click", () => { currentDate.setMonth(currentDate.getMonth() + 1); renderCalendar(); });
  renderCalendar();

  /* ================= SILLAS ================= */
  const chairSlides = document.querySelectorAll(".chair-slide");
  const prevChair = document.getElementById("prevChair");
  const nextChair = document.getElementById("nextChair");
  const selectedChair = document.getElementById("selectedChair");
  let currentChair = 0;

  function showChair(index){
    chairSlides.forEach(slide => slide.classList.remove("active-chair"));
    chairSlides[index].classList.add("active-chair");
    selectedChair.textContent = chairSlides[index].querySelector('.label').textContent.trim();
  }
  nextChair.addEventListener("click", () => { currentChair = (currentChair + 1) % chairSlides.length; showChair(currentChair); });
  prevChair.addEventListener("click", () => { currentChair = (currentChair - 1 + chairSlides.length) % chairSlides.length; showChair(currentChair); });
  showChair(currentChair);

  /* ================= MODAL WIZARD ================= */
  const bookingModal = document.getElementById("bookingModal");
  const openBookingModal = document.getElementById("openBookingModal");
  const closeModal = document.getElementById("closeModal");
  const modalSub = document.getElementById("modalSub");
  const bars = { 1: document.getElementById('bar1'), 2: document.getElementById('bar2'), 3: document.getElementById('bar3') };
  const stepLabels = { 1: "Paso 1 de 3 — Tus datos", 2: "Paso 2 de 3 — Horario", 3: "Paso 3 de 3 — Confirmar" };

  function goToStep(n){
    document.querySelectorAll('.wizard-step').forEach(s => s.classList.remove('active'));
    document.querySelector(`.wizard-step[data-step="${n}"]`).classList.add('active');

    if (n <= 3){
      modalSub.style.display = 'block';
      document.querySelector('.steps-bar').style.display = 'flex';
      modalSub.textContent = stepLabels[n];
      Object.keys(bars).forEach(k => {
        bars[k].classList.remove('current','done');
        if (k < n) bars[k].classList.add('done');
        if (k == n) bars[k].classList.add('current');
      });
    } else {
      modalSub.style.display = 'none';
      document.querySelector('.steps-bar').style.display = 'none';
    }

    if (n === 3) buildSummary();
    if (n === 2) document.getElementById('chairDisplay').value = selectedChair.textContent;
  }

  openBookingModal.addEventListener("click", () => {
    bookingModal.classList.add("active");
    goToStep(1);
  });
  closeModal.addEventListener("click", () => bookingModal.classList.remove("active"));
  bookingModal.addEventListener("click", (e) => { if (e.target === bookingModal) bookingModal.classList.remove("active"); });

  document.querySelectorAll('[data-next]').forEach(btn => {
    btn.addEventListener('click', () => {
      const step = btn.closest('.wizard-step');
      const inputs = step.querySelectorAll('input[required], select[required]');
      let valid = true;
      inputs.forEach(inp => { if (!inp.value){ valid = false; inp.style.borderColor = '#e2685a'; } else { inp.style.borderColor = ''; } });
      if (!valid) return;
      goToStep(parseInt(btn.dataset.next));
    });
  });
  document.querySelectorAll('[data-back]').forEach(btn => {
    btn.addEventListener('click', () => goToStep(parseInt(btn.dataset.back)));
  });

  function buildSummary(){
    const nombre = document.getElementById('nombre').value || '—';
    const servicio = document.getElementById('servicio').value || '—';
    const hora = timeSelect.value || '—';
    const box = document.getElementById('summaryBox');
    box.innerHTML = `
      <div class="summary-row"><span>Nombre</span><span>${nombre}</span></div>
      <div class="summary-row"><span>Servicio</span><span>${servicio}</span></div>
      <div class="summary-row"><span>Silla</span><span>${selectedChair.textContent}</span></div>
      <div class="summary-row"><span>Fecha</span><span>${selectedDateText || 'Selecciona una fecha en el calendario'}</span></div>
      <div class="summary-row"><span>Hora</span><span>${hora}</span></div>
    `;
  }

  document.getElementById('bookingForm').addEventListener('submit', (e) => {
    e.preventDefault();
    if (!timeSelect.value){
      timeSelect.style.borderColor = '#e2685a';
      goToStep(2);
      return;
    }
    goToStep(4);
  });

  document.getElementById('finishBooking').addEventListener('click', () => {
    bookingModal.classList.remove('active');
    document.getElementById('bookingForm').reset();
    setTimeout(() => goToStep(1), 300);
  });
</script>

</body>
</html>