<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RSKF Group of Companies Ltd.</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; scroll-behavior:smooth; }
    body { font-family:'Segoe UI',Arial,sans-serif; background:#0a0a0a; color:white; overflow-x:hidden; }

    #particles { position:fixed; top:0; left:0; width:100%; height:100%; z-index:0; pointer-events:none; }

    .navbar {
      position:fixed; top:0; left:0; width:100%; z-index:1000;
      padding:18px 60px; display:flex; justify-content:space-between; align-items:center;
      background:rgba(0,0,0,0.8); backdrop-filter:blur(20px);
      border-bottom:1px solid rgba(255,255,255,0.06); transition:all 0.3s;
    }
    .navbar.scrolled { padding:12px 60px; background:rgba(0,0,0,0.95); }
    .navbar .logo { font-size:18px; font-weight:800; color:white; letter-spacing:0.5px; }
    .navbar .logo span { color:#c0392b; }
    .navbar nav a { color:rgba(255,255,255,0.7); text-decoration:none; margin-left:30px; font-size:13px; transition:color 0.2s; }
    .navbar nav a:hover { color:#f39c12; }
    .navbar .nav-btn { margin-left:20px; background:linear-gradient(135deg,#c0392b,#e74c3c); color:white; padding:9px 22px; border-radius:8px; font-size:13px; text-decoration:none; font-weight:600; transition:all 0.2s; }
    .navbar .nav-btn:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(192,57,43,0.4); }

    .hero {
      min-height:100vh; display:flex; align-items:center; justify-content:center;
      text-align:center; padding:140px 40px 100px; position:relative; overflow:hidden;
      background:linear-gradient(135deg, #0a0a0a 0%, #1a0000 40%, #0a0a0a 100%);
    }
    .hero-bg {
      position:absolute; top:0; left:0; width:100%; height:100%;
      background:url('https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=1920&q=80') center/cover;
      opacity:0.08; filter:blur(2px);
    }
    .hero::before {
      content:''; position:absolute; top:0; left:0; width:100%; height:100%;
      background:radial-gradient(ellipse at 30% 50%, rgba(192,57,43,0.2) 0%, transparent 60%),
                 radial-gradient(ellipse at 70% 50%, rgba(243,156,18,0.08) 0%, transparent 60%);
      animation:heroGlow 6s ease-in-out infinite alternate;
    }
    @keyframes heroGlow { 0%{opacity:0.5} 100%{opacity:1} }

    .hero-content { position:relative; z-index:1; max-width:900px; }
    .hero-badge {
      display:inline-flex; align-items:center; gap:8px;
      background:rgba(243,156,18,0.12); border:1px solid rgba(243,156,18,0.35);
      color:#f39c12; padding:8px 20px; border-radius:30px; font-size:12px;
      letter-spacing:1.5px; text-transform:uppercase; margin-bottom:30px;
      animation:fadeDown 0.8s ease forwards;
    }
    .hero-badge .dot { width:6px; height:6px; background:#f39c12; border-radius:50%; animation:blink 1.5s infinite; }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0} }

    .hero h1 {
      font-size:68px; font-weight:900; line-height:1.1;
      margin-bottom:25px; letter-spacing:-2px;
      animation:fadeUp 0.8s ease 0.2s both;
      background:linear-gradient(135deg, #ffffff 0%, rgba(255,255,255,0.75) 100%);
      -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
    }
    .hero h1 em { font-style:normal; background:linear-gradient(135deg,#c0392b,#e74c3c); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
    .hero p { font-size:18px; color:rgba(255,255,255,0.55); max-width:650px; margin:0 auto 45px; line-height:1.8; animation:fadeUp 0.8s ease 0.4s both; }

    .hero-btns { display:flex; gap:15px; justify-content:center; flex-wrap:wrap; animation:fadeUp 0.8s ease 0.6s both; }
    .btn-primary { background:linear-gradient(135deg,#c0392b,#e74c3c); color:white; padding:16px 40px; border-radius:10px; text-decoration:none; font-size:15px; font-weight:700; transition:all 0.3s; }
    .btn-primary:hover { transform:translateY(-3px); box-shadow:0 12px 30px rgba(192,57,43,0.5); }
    .btn-secondary { background:rgba(243,156,18,0.08); color:#f39c12; padding:16px 40px; border-radius:10px; text-decoration:none; font-size:15px; border:1px solid rgba(243,156,18,0.25); transition:all 0.3s; font-weight:600; }
    .btn-secondary:hover { background:rgba(243,156,18,0.15); transform:translateY(-3px); }

    .hero-cards { display:flex; gap:15px; justify-content:center; margin-top:60px; animation:fadeUp 0.8s ease 0.8s both; flex-wrap:wrap; }
    .hero-card { background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:16px 22px; display:flex; align-items:center; gap:10px; backdrop-filter:blur(10px); transition:all 0.3s; }
    .hero-card:hover { background:rgba(243,156,18,0.08); border-color:rgba(243,156,18,0.2); transform:translateY(-3px); }
    .hero-card .hc-icon { font-size:24px; }
    .hero-card .hc-text { font-size:13px; color:rgba(255,255,255,0.6); }
    .hero-card .hc-text strong { display:block; color:white; font-size:15px; }

    .scroll-indicator { position:absolute; bottom:35px; left:50%; transform:translateX(-50%); animation:bounce 2s infinite; }
    .scroll-indicator div { width:26px; height:44px; border:2px solid rgba(243,156,18,0.3); border-radius:13px; display:flex; align-items:flex-start; justify-content:center; padding-top:7px; }
    .scroll-indicator div::after { content:''; width:4px; height:8px; background:#f39c12; border-radius:2px; animation:scrollDown 1.5s infinite; }
    @keyframes scrollDown { 0%{transform:translateY(0);opacity:1} 100%{transform:translateY(14px);opacity:0} }
    @keyframes bounce { 0%,100%{transform:translateX(-50%) translateY(0)} 50%{transform:translateX(-50%) translateY(-8px)} }

    .stats { background:#111; padding:70px 60px; border-top:1px solid rgba(255,255,255,0.05); border-bottom:1px solid rgba(243,156,18,0.08); position:relative; z-index:1; }
    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:30px; max-width:1000px; margin:0 auto; }
    .stat-item { text-align:center; }
    .stat-item .num { font-size:48px; font-weight:900; color:#f39c12; display:block; line-height:1; }
    .stat-item .label { font-size:13px; color:rgba(255,255,255,0.4); margin-top:8px; letter-spacing:0.5px; }

    section { padding:100px 60px; position:relative; z-index:1; }
    .section-label { font-size:11px; text-transform:uppercase; letter-spacing:3px; color:#f39c12; margin-bottom:12px; display:block; }
    .section-title { font-size:40px; font-weight:800; margin-bottom:15px; line-height:1.2; }
    .section-sub { font-size:15px; color:rgba(255,255,255,0.45); max-width:500px; line-height:1.8; }

    .brands { background:#0d0d0d; padding:50px 60px; position:relative; z-index:1; }
    .brands-label { text-align:center; font-size:13px; color:rgba(243,156,18,0.6); letter-spacing:2px; text-transform:uppercase; margin-bottom:35px; }
    .brands-grid { display:flex; gap:20px; justify-content:center; flex-wrap:wrap; max-width:1100px; margin:0 auto; }
    .brand-pill { background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:12px 24px; font-size:13px; color:rgba(255,255,255,0.6); font-weight:600; transition:all 0.3s; cursor:default; }
    .brand-pill:hover { background:rgba(243,156,18,0.1); border-color:rgba(243,156,18,0.3); color:#f39c12; transform:translateY(-2px); }

    .products { background:#0a0a0a; }
    .products-header { text-align:center; max-width:700px; margin:0 auto 60px; }
    .products-header .section-sub { margin:0 auto; }

    .cat-filter { display:flex; gap:10px; justify-content:center; flex-wrap:wrap; margin-bottom:40px; }
    .cat-btn { padding:9px 22px; border-radius:25px; font-size:13px; color:rgba(255,255,255,0.5); background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08); cursor:pointer; transition:all 0.3s; font-weight:600; }
    .cat-btn:hover, .cat-btn.active { background:rgba(243,156,18,0.15); color:#f39c12; border-color:rgba(243,156,18,0.4); }

    .products-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:25px; max-width:1100px; margin:0 auto; }
    .product-card { background:#111; border:1px solid rgba(255,255,255,0.06); border-radius:16px; overflow:hidden; cursor:pointer; transition:all 0.4s; position:relative; }
    .product-card:hover { transform:translateY(-8px); border-color:rgba(243,156,18,0.4); box-shadow:0 25px 50px rgba(0,0,0,0.5), 0 0 30px rgba(243,156,18,0.05); }
    .product-card:hover .product-overlay { opacity:1; }
    .product-img { height:220px; overflow:hidden; position:relative; }
    .product-img img { width:100%; height:100%; object-fit:cover; transition:transform 0.6s; }
    .product-card:hover .product-img img { transform:scale(1.1); }
    .product-overlay { position:absolute; top:0; left:0; width:100%; height:100%; background:linear-gradient(135deg,rgba(192,57,43,0.7),rgba(243,156,18,0.5)); display:flex; align-items:center; justify-content:center; opacity:0; transition:all 0.3s; }
    .product-overlay span { color:white; font-size:14px; font-weight:700; letter-spacing:1px; }
    .product-badge { position:absolute; top:12px; left:12px; background:linear-gradient(135deg,#f39c12,#e67e22); color:white; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700; letter-spacing:0.5px; }
    .product-badge.red { background:linear-gradient(135deg,#c0392b,#e74c3c); }
    .product-body { padding:22px; }
    .product-cat { font-size:11px; color:#f39c12; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:8px; font-weight:700; }
    .product-name { font-size:16px; font-weight:700; margin-bottom:8px; line-height:1.4; }
    .product-desc { font-size:12px; color:rgba(255,255,255,0.4); margin-bottom:15px; line-height:1.6; }
    .product-footer { display:flex; justify-content:space-between; align-items:center; }
    .product-price { font-size:20px; font-weight:900; color:#f39c12; }
    .product-unit  { font-size:11px; color:rgba(255,255,255,0.3); margin-top:2px; }
    .product-brands { display:flex; gap:5px; flex-wrap:wrap; }
    .product-brand-tag { background:rgba(255,255,255,0.06); color:rgba(255,255,255,0.5); padding:3px 8px; border-radius:5px; font-size:10px; }

    .modal-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.88); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(8px); }
    .modal-overlay.open { display:flex; }
    .product-modal { background:#111; border:1px solid rgba(243,156,18,0.15); border-radius:20px; width:900px; max-width:95vw; max-height:90vh; overflow-y:auto; display:flex; animation:modalIn 0.4s ease; position:relative; }
    @keyframes modalIn { from{opacity:0;transform:scale(0.9) translateY(20px)} to{opacity:1;transform:scale(1) translateY(0)} }
    .modal-img { width:400px; flex-shrink:0; }
    .modal-img img { width:100%; height:100%; object-fit:cover; border-radius:20px 0 0 20px; }
    .modal-body { padding:40px; flex:1; }
    .modal-close { position:absolute; top:15px; right:15px; background:rgba(255,255,255,0.1); border:none; color:white; width:36px; height:36px; border-radius:50%; cursor:pointer; font-size:18px; transition:all 0.2s; z-index:10; }
    .modal-close:hover { background:rgba(243,156,18,0.2); color:#f39c12; }
    .modal-cat { font-size:11px; color:#f39c12; text-transform:uppercase; letter-spacing:2px; margin-bottom:12px; font-weight:700; }
    .modal-title { font-size:26px; font-weight:800; margin-bottom:12px; line-height:1.3; }
    .modal-price { font-size:36px; font-weight:900; color:#f39c12; margin-bottom:5px; }
    .modal-unit  { font-size:13px; color:rgba(255,255,255,0.4); margin-bottom:20px; }
    .modal-desc  { font-size:14px; color:rgba(255,255,255,0.6); line-height:1.8; margin-bottom:25px; }
    .modal-brands-title { font-size:11px; color:rgba(243,156,18,0.7); text-transform:uppercase; letter-spacing:1px; margin-bottom:12px; }
    .modal-brands-grid { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:25px; }
    .modal-brand { background:rgba(243,156,18,0.08); border:1px solid rgba(243,156,18,0.2); color:#f39c12; padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600; }
    .modal-features { margin-bottom:28px; }
    .modal-features h4 { font-size:11px; color:rgba(255,255,255,0.4); text-transform:uppercase; letter-spacing:1px; margin-bottom:12px; }
    .modal-feature { display:flex; align-items:center; gap:8px; margin-bottom:9px; font-size:13px; color:rgba(255,255,255,0.7); }
    .modal-feature::before { content:'✓'; color:#f39c12; font-weight:900; }
    .modal-btn { display:block; text-align:center; background:linear-gradient(135deg,#c0392b,#e74c3c); color:white; padding:14px; border-radius:10px; text-decoration:none; font-size:15px; font-weight:700; transition:all 0.3s; }
    .modal-btn:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(192,57,43,0.4); }

    .about { background:#0d0d0d; }
    .about-grid { display:grid; grid-template-columns:1fr 1fr; gap:80px; align-items:center; max-width:1100px; margin:0 auto; }
    .about-features { margin-top:35px; }
    .feature-item { display:flex; align-items:flex-start; gap:16px; margin-bottom:24px; }
    .feature-icon { width:44px; height:44px; background:rgba(243,156,18,0.1); border:1px solid rgba(243,156,18,0.2); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
    .feature-text h4 { font-size:15px; font-weight:700; margin-bottom:4px; }
    .feature-text p  { font-size:13px; color:rgba(255,255,255,0.45); line-height:1.6; }
    .about-visual { background:linear-gradient(135deg,#1a0a00,#2d1500); border-radius:20px; padding:45px; border:1px solid rgba(243,156,18,0.12); position:relative; overflow:hidden; }
    .about-visual::before { content:''; position:absolute; top:-50%; right:-50%; width:200px; height:200px; border-radius:50%; background:rgba(243,156,18,0.04); }
    .about-visual .big-text { font-size:80px; font-weight:900; color:rgba(243,156,18,0.12); line-height:1; margin-bottom:20px; }
    .about-visual .highlight { font-size:24px; font-weight:800; margin-bottom:15px; color:#f39c12; }
    .about-visual p { font-size:14px; color:rgba(255,255,255,0.45); line-height:1.9; }
    .gold-badge { display:inline-flex; align-items:center; gap:8px; background:linear-gradient(135deg,#f39c12,#e67e22); padding:10px 18px; border-radius:10px; margin-top:22px; font-size:13px; font-weight:700; color:white; }

    .ceo { background:#0a0a0a; }
    .ceo-grid { display:grid; grid-template-columns:1fr 2fr; gap:60px; align-items:center; max-width:1000px; margin:50px auto 0; }
    .ceo-avatar { width:240px; height:240px; border-radius:50%; margin:0 auto; overflow:hidden; box-shadow:0 0 0 8px rgba(243,156,18,0.1), 0 0 0 16px rgba(243,156,18,0.05), 0 0 0 24px rgba(243,156,18,0.02); }
    .ceo-avatar img { width:100%; height:100%; object-fit:cover; }
    .ceo-info .name  { font-size:32px; font-weight:900; margin-bottom:5px; }
    .ceo-info .title { color:#f39c12; font-size:13px; letter-spacing:2px; text-transform:uppercase; margin-bottom:20px; }
    .ceo-info .bio   { font-size:15px; color:rgba(255,255,255,0.55); line-height:1.9; margin-bottom:25px; }
    .ceo-badges { display:flex; gap:10px; flex-wrap:wrap; }
    .ceo-badge { background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); padding:7px 16px; border-radius:8px; font-size:12px; color:rgba(255,255,255,0.65); }
    .ceo-badge.gold { background:rgba(243,156,18,0.12); border-color:rgba(243,156,18,0.3); color:#f39c12; }

    .portals { background:#0d0d0d; }
    .portals-header { text-align:center; max-width:600px; margin:0 auto 55px; }
    .portals-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:25px; max-width:1000px; margin:0 auto; }
    .portal-card { border-radius:16px; padding:40px 30px; text-align:center; text-decoration:none; color:white; transition:all 0.4s; border:1px solid rgba(255,255,255,0.06); position:relative; overflow:hidden; }
    .portal-card::before { content:''; position:absolute; top:0; left:0; width:100%; height:100%; opacity:0; transition:opacity 0.3s; }
    .portal-card:hover { transform:translateY(-8px); }
    .portal-card:hover::before { opacity:1; }
    .portal-card.customer { background:linear-gradient(135deg,#5c0000,#c0392b); }
    .portal-card.customer::before { background:linear-gradient(135deg,#7b0000,#e74c3c); }
    .portal-card.supplier { background:linear-gradient(135deg,#0d2137,#2980b9); }
    .portal-card.supplier::before { background:linear-gradient(135deg,#1a3a5c,#3498db); }
    .portal-card.employee { background:linear-gradient(135deg,#0d2b14,#27ae60); }
    .portal-card.employee::before { background:linear-gradient(135deg,#1a4a2a,#2ecc71); }
    .portal-card .p-icon { font-size:48px; margin-bottom:18px; position:relative; z-index:1; }
    .portal-card h3 { font-size:20px; font-weight:800; margin-bottom:10px; position:relative; z-index:1; }
    .portal-card p  { font-size:13px; opacity:0.75; margin-bottom:22px; line-height:1.6; position:relative; z-index:1; }
    .portal-card .p-btn { display:inline-block; background:rgba(255,255,255,0.2); padding:10px 26px; border-radius:8px; font-size:13px; font-weight:700; position:relative; z-index:1; transition:background 0.2s; }
    .portal-card:hover .p-btn { background:rgba(255,255,255,0.3); }

    .contact { background:#0a0a0a; }
    .contact-grid { display:grid; grid-template-columns:1fr 1fr; gap:60px; max-width:1000px; margin:50px auto 0; align-items:start; }
    .contact-item { display:flex; gap:16px; margin-bottom:28px; align-items:flex-start; }
    .contact-icon { width:46px; height:46px; background:rgba(243,156,18,0.1); border:1px solid rgba(243,156,18,0.2); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
    .contact-text h4 { font-size:14px; font-weight:700; margin-bottom:4px; color:#f39c12; }
    .contact-text p, .contact-text a { font-size:13px; color:rgba(255,255,255,0.45); text-decoration:none; }
    .contact-text a:hover { color:#f39c12; }
    .register-box { background:linear-gradient(135deg,rgba(243,156,18,0.1),rgba(192,57,43,0.08)); border:1px solid rgba(243,156,18,0.2); border-radius:16px; padding:35px; text-align:center; }
    .register-box h3 { font-size:20px; font-weight:800; margin-bottom:10px; color:#f39c12; }
    .register-box p  { font-size:13px; color:rgba(255,255,255,0.45); margin-bottom:22px; line-height:1.7; }
    .register-box a  { display:inline-block; background:linear-gradient(135deg,#c0392b,#e74c3c); color:white; padding:13px 32px; border-radius:10px; text-decoration:none; font-size:14px; font-weight:700; transition:all 0.3s; }
    .register-box a:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(192,57,43,0.4); }

    .footer { background:#000; padding:30px 60px; display:flex; justify-content:space-between; align-items:center; border-top:1px solid rgba(243,156,18,0.08); flex-wrap:wrap; gap:15px; }
    .footer p { font-size:13px; color:rgba(255,255,255,0.25); }
    .footer .f-links a { color:rgba(255,255,255,0.25); text-decoration:none; margin-left:20px; font-size:13px; transition:color 0.2s; }
    .footer .f-links a:hover { color:#f39c12; }

    /* Divider line yellow accent */
    .yellow-divider { height:2px; background:linear-gradient(90deg,transparent,rgba(243,156,18,0.4),transparent); margin:0; }

    @keyframes fadeUp   { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }
    @keyframes fadeDown { from{opacity:0;transform:translateY(-20px)} to{opacity:1;transform:translateY(0)} }
    .reveal { opacity:0; transform:translateY(40px); transition:all 0.8s ease; }
    .reveal.visible { opacity:1; transform:translateY(0); }
    .reveal-left  { opacity:0; transform:translateX(-40px); transition:all 0.8s ease; }
    .reveal-left.visible  { opacity:1; transform:translateX(0); }
    .reveal-right { opacity:0; transform:translateX(40px); transition:all 0.8s ease; }
    .reveal-right.visible { opacity:1; transform:translateX(0); }
  </style>
</head>
<body>

<canvas id="particles"></canvas>

<div class="navbar" id="navbar">
  <div class="logo">RSKF <span>Group</span></div>
  <nav>
    <a href="#about">About</a>
    <a href="#products">Products</a>
    <a href="#ceo">Leadership</a>
    <a href="#contact">Contact</a>
    <a href="register.php" class="nav-btn">Get Started</a>
  </nav>
</div>

<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-content">
    <div class="hero-badge"><span class="dot"></span> Gold Medalist — 3 Years in Building Materials</div>
    <h1>Building Pakistan's<br><em>Future Together</em></h1>
    <p>RSKF Group of Companies Ltd. — Your trusted partner for premium quality building materials from Pakistan's top brands. Serving contractors, builders and developers nationwide.</p>
    <div class="hero-btns">
      <a href="register.php" class="btn-primary">Start Ordering Today</a>
      <a href="#products"    class="btn-secondary">Browse Products ✦</a>
    </div>
    <div class="hero-cards">
      <div class="hero-card"><span class="hc-icon">🏗️</span><div class="hc-text"><strong>50+</strong>Products</div></div>
      <div class="hero-card"><span class="hc-icon">🏆</span><div class="hc-text"><strong>Gold</strong>Medalist</div></div>
      <div class="hero-card"><span class="hc-icon">🚚</span><div class="hc-text"><strong>Fast</strong>Delivery</div></div>
      <div class="hero-card"><span class="hc-icon">⭐</span><div class="hc-text"><strong>500+</strong>Customers</div></div>
      <div class="hero-card"><span class="hc-icon">📍</span><div class="hc-text"><strong>Multan</strong>Pakistan</div></div>
    </div>
  </div>
  <div class="scroll-indicator"><div></div></div>
</section>

<div class="yellow-divider"></div>

<div class="stats reveal">
  <div class="stats-grid">
    <div class="stat-item"><span class="num" data-target="5">0</span><div class="label">Years in Business</div></div>
    <div class="stat-item"><span class="num" data-target="500">0</span><div class="label">Happy Customers</div></div>
    <div class="stat-item"><span class="num" data-target="50">0</span><div class="label">Products Available</div></div>
    <div class="stat-item"><span class="num" data-target="3">0</span><div class="label">Gold Medal Years</div></div>
  </div>
</div>

<div class="yellow-divider"></div>

<div class="brands reveal">
  <div class="brands-label">✦ Authorized Dealer — Top Pakistani Brands ✦</div>
  <div class="brands-grid">
    <div class="brand-pill">🏭 Lucky Cement</div>
    <div class="brand-pill">🏭 DG Khan Cement</div>
    <div class="brand-pill">🏭 Maple Leaf Cement</div>
    <div class="brand-pill">⚙️ Ittefaq Steel</div>
    <div class="brand-pill">⚙️ Amreli Steels</div>
    <div class="brand-pill">⚙️ Mughal Steel</div>
    <div class="brand-pill">🧱 Shabbir Bricks</div>
    <div class="brand-pill">🎨 Berger Paints</div>
    <div class="brand-pill">🎨 ICI Dulux</div>
    <div class="brand-pill">🎨 Nippon Paint</div>
    <div class="brand-pill">🔧 Supreme Pipes</div>
    <div class="brand-pill">🔧 Master Pipes</div>
  </div>
</div>

<section class="products reveal" id="products">
  <div class="products-header">
    <span class="section-label">✦ Our Products</span>
    <h2 class="section-title">Premium Building Materials</h2>
    <p class="section-sub">Click on any product to view details, available brands and pricing</p>
  </div>

  <div class="cat-filter">
    <button class="cat-btn active" onclick="filterProducts('all', this)">All Products</button>
    <button class="cat-btn" onclick="filterProducts('cement', this)">🏗️ Cement</button>
    <button class="cat-btn" onclick="filterProducts('steel', this)">⚙️ Steel</button>
    <button class="cat-btn" onclick="filterProducts('bricks', this)">🧱 Bricks</button>
    <button class="cat-btn" onclick="filterProducts('sand', this)">🪨 Sand & Gravel</button>
    <button class="cat-btn" onclick="filterProducts('pipes', this)">🔧 Pipes</button>
    <button class="cat-btn" onclick="filterProducts('paint', this)">🎨 Paint</button>
  </div>

  <div class="products-grid" id="productsGrid">
    <div class="product-card" data-cat="cement" onclick="openProduct('cement')">
      <div class="product-img">
        <img src="https://images.unsplash.com/photo-1589939705384-5185137a7f0f?w=600&q=80" alt="Cement">
        <div class="product-overlay"><span>VIEW DETAILS →</span></div>
        <div class="product-badge">BESTSELLER</div>
      </div>
      <div class="product-body">
        <div class="product-cat">Cement</div>
        <div class="product-name">Ordinary Portland Cement 50kg</div>
        <div class="product-desc">High strength cement for all construction work</div>
        <div class="product-footer">
          <div><div class="product-price">Rs. 950</div><div class="product-unit">per Bag</div></div>
          <div class="product-brands">
            <span class="product-brand-tag">Lucky</span>
            <span class="product-brand-tag">DG Khan</span>
            <span class="product-brand-tag">Maple</span>
          </div>
        </div>
      </div>
    </div>

    <div class="product-card" data-cat="steel" onclick="openProduct('steel')">
      <div class="product-img">
        <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=600&q=80" alt="Steel">
        <div class="product-overlay"><span>VIEW DETAILS →</span></div>
        <div class="product-badge red">PREMIUM</div>
      </div>
      <div class="product-body">
        <div class="product-cat">Steel</div>
        <div class="product-name">Steel Rod 12mm — High Tensile</div>
        <div class="product-desc">Grade 60 reinforcement bars for concrete structures</div>
        <div class="product-footer">
          <div><div class="product-price">Rs. 95,000</div><div class="product-unit">per Ton</div></div>
          <div class="product-brands">
            <span class="product-brand-tag">Ittefaq</span>
            <span class="product-brand-tag">Amreli</span>
            <span class="product-brand-tag">Mughal</span>
          </div>
        </div>
      </div>
    </div>

    <div class="product-card" data-cat="bricks" onclick="openProduct('bricks')">
      <div class="product-img">
        <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80" alt="Bricks">
        <div class="product-overlay"><span>VIEW DETAILS →</span></div>
      </div>
      <div class="product-body">
        <div class="product-cat">Bricks</div>
        <div class="product-name">Red Brick Standard — Grade A</div>
        <div class="product-desc">Premium quality red bricks for walls and foundations</div>
        <div class="product-footer">
          <div><div class="product-price">Rs. 12</div><div class="product-unit">per Piece</div></div>
          <div class="product-brands">
            <span class="product-brand-tag">Shabbir</span>
            <span class="product-brand-tag">Local</span>
          </div>
        </div>
      </div>
    </div>

    <div class="product-card" data-cat="sand" onclick="openProduct('sand')">
      <div class="product-img">
        <img src="https://images.pexels.com/photos/1029604/pexels-photo-1029604.jpeg?w=600" alt="Sand">
        <div class="product-overlay"><span>VIEW DETAILS →</span></div>
      </div>
      <div class="product-body">
        <div class="product-cat">Sand & Gravel</div>
        <div class="product-name">Crush Sand — Fine Grade</div>
        <div class="product-desc">Clean crushed sand for concrete mixing and plastering</div>
        <div class="product-footer">
          <div><div class="product-price">Rs. 8,000</div><div class="product-unit">per Trolley</div></div>
          <div class="product-brands">
            <span class="product-brand-tag">Local</span>
          </div>
        </div>
      </div>
    </div>

    <div class="product-card" data-cat="pipes" onclick="openProduct('pipes')">
      <div class="product-img">
        <img src="https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=600&q=80" alt="Pipes">
        <div class="product-overlay"><span>VIEW DETAILS →</span></div>
      </div>
      <div class="product-body">
        <div class="product-cat">Pipes & Fittings</div>
        <div class="product-name">PVC Pipe 4 inch — Heavy Duty</div>
        <div class="product-desc">Durable PVC pipes for drainage and plumbing systems</div>
        <div class="product-footer">
          <div><div class="product-price">Rs. 450</div><div class="product-unit">per Piece</div></div>
          <div class="product-brands">
            <span class="product-brand-tag">Supreme</span>
            <span class="product-brand-tag">Master</span>
          </div>
        </div>
      </div>
    </div>

    <div class="product-card" data-cat="paint" onclick="openProduct('paint')">
      <div class="product-img">
        <img src="https://images.unsplash.com/photo-1562259949-e8e7689d7828?w=600&q=80" alt="Paint">
        <div class="product-overlay"><span>VIEW DETAILS →</span></div>
        <div class="product-badge">NEW</div>
      </div>
      <div class="product-body">
        <div class="product-cat">Paint & Chemical</div>
        <div class="product-name">Weather Shield Paint 20L</div>
        <div class="product-desc">Premium weather resistant exterior wall paint</div>
        <div class="product-footer">
          <div><div class="product-price">Rs. 3,500</div><div class="product-unit">per Tin</div></div>
          <div class="product-brands">
            <span class="product-brand-tag">Berger</span>
            <span class="product-brand-tag">ICI</span>
            <span class="product-brand-tag">Nippon</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="yellow-divider"></div>

<section class="about reveal" id="about">
  <div class="about-grid">
    <div class="reveal-left">
      <span class="section-label">✦ About Us</span>
      <h2 class="section-title">Who We Are</h2>
      <p class="section-sub">RSKF Group of Companies Ltd. is a leading building materials supplier based in Multan, Pakistan. With 5 years of excellence, we deliver quality products from Pakistan's top brands to builders and developers nationwide.</p>
      <div class="about-features">
        <div class="feature-item">
          <div class="feature-icon">🏆</div>
          <div class="feature-text"><h4>Award Winning</h4><p>Gold Medalist in building materials supply for 3 consecutive years</p></div>
        </div>
        <div class="feature-item">
          <div class="feature-icon">🏭</div>
          <div class="feature-text"><h4>Authorized Dealer</h4><p>Official dealer of Lucky, DG Khan, Ittefaq, Berger and more</p></div>
        </div>
        <div class="feature-item">
          <div class="feature-icon">🚚</div>
          <div class="feature-text"><h4>Fast Delivery</h4><p>On-time delivery to construction sites across Punjab</p></div>
        </div>
      </div>
    </div>
    <div class="reveal-right">
      <div class="about-visual">
        <div class="big-text">5+</div>
        <div class="highlight">Years of Trust & Excellence</div>
        <p>From a small startup in Multan to one of South Punjab's most trusted building materials companies — RSKF Group has grown through dedication, quality and customer satisfaction.</p>
        <div class="gold-badge">🥇 Gold Medalist — 2022, 2023, 2024</div>
      </div>
    </div>
  </div>
</section>

<section class="ceo reveal" id="ceo">
  <div style="text-align:center">
    <span class="section-label">✦ Leadership</span>
    <h2 class="section-title">Meet Our CEO</h2>
  </div>
  <div class="ceo-grid">
    <div style="text-align:center">
      <div class="ceo-avatar">
        <img src="ceo.jpg" alt="Muhammad Abrar Khan" onerror="this.src='https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&q=80'">
      </div>
      <div style="margin-top:20px;font-size:13px;color:rgba(243,156,18,0.5)">Chief Executive Officer</div>
    </div>
    <div class="ceo-info">
      <div class="name">Muhammad Abrar Khan</div>
      <div class="title">Founder & Chief Executive Officer</div>
      <p class="bio">Muhammad Abrar Khan is the visionary founder of RSKF Group of Companies Ltd., based in Multan, Pakistan. With over 5 years of experience in the building materials industry, he has transformed RSKF into one of South Punjab's most trusted suppliers and authorized dealers of Pakistan's top brands.</p>
      <div class="ceo-badges">
        <span class="ceo-badge">📍 Multan, Pakistan</span>
        <span class="ceo-badge">🏢 5+ Years Experience</span>
        <span class="ceo-badge gold">🥇 Gold Medalist x3</span>
        <span class="ceo-badge">📞 0314-6088095</span>
      </div>
    </div>
  </div>
</section>

<div class="yellow-divider"></div>

<section class="portals reveal">
  <div class="portals-header">
    <span class="section-label">✦ Access Portals</span>
    <h2 class="section-title">Choose Your Portal</h2>
    <p class="section-sub">Login or create an account to access your personalized dashboard</p>
  </div>
  <div class="portals-grid">
    <a href="customer_login.php" class="portal-card customer">
      <div class="p-icon">👤</div>
      <h3>Customer Portal</h3>
      <p>Browse products, place orders and track deliveries</p>
      <span class="p-btn">Customer Login →</span>
    </a>
    <a href="supplier_login.php" class="portal-card supplier">
      <div class="p-icon">🏭</div>
      <h3>Supplier Portal</h3>
      <p>Manage your supply contracts and deliveries</p>
      <span class="p-btn">Supplier Login →</span>
    </a>
    <a href="employee_login.php" class="portal-card employee">
      <div class="p-icon">👷</div>
      <h3>Employee Portal</h3>
      <p>View your profile, salary and department info</p>
      <span class="p-btn">Employee Login →</span>
    </a>
  </div>
</section>

<section class="contact reveal" id="contact">
  <div style="text-align:center">
    <span class="section-label">✦ Contact Us</span>
    <h2 class="section-title">Get In Touch</h2>
  </div>
  <div class="contact-grid">
    <div>
      <div class="contact-item"><div class="contact-icon">📍</div><div class="contact-text"><h4>Location</h4><p>Multan, Punjab, Pakistan</p></div></div>
      <div class="contact-item"><div class="contact-icon">📞</div><div class="contact-text"><h4>Phone</h4><p><a href="tel:03146088095">0314-6088095</a></p></div></div>
      <div class="contact-item"><div class="contact-icon">✉️</div><div class="contact-text"><h4>Email</h4><p><a href="mailto:sonofrajpoot2005@gmail.com">sonofrajpoot2005@gmail.com</a></p></div></div>
      <div class="contact-item"><div class="contact-icon">🕐</div><div class="contact-text"><h4>Business Hours</h4><p>Monday – Saturday: 9:00 AM – 6:00 PM</p></div></div>
    </div>
    <div class="register-box">
      <h3>🚀 Join RSKF Today</h3>
      <p>Create your account and start ordering premium building materials from Pakistan's top brands directly through RSKF Group.</p>
      <a href="register.php">Create Account →</a>
    </div>
  </div>
</section>

<div class="footer">
  <p>&copy; <?php echo date('Y'); ?> RSKF Group of Companies Ltd. — All Rights Reserved | Multan, Pakistan</p>
  <div class="f-links">
    <a href="register.php">Register</a>
    <a href="customer_login.php">Customer</a>
    <a href="supplier_login.php">Supplier</a>
    <a href="employee_login.php">Employee</a>
  </div>
</div>

<div class="modal-overlay" id="productModal" onclick="closeModal(event)">
  <div class="product-modal" id="modalContent">
    <button class="modal-close" onclick="closeProductModal()">✕</button>
    <div class="modal-img"><img id="modalImg" src="" alt="Product"></div>
    <div class="modal-body">
      <div class="modal-cat"   id="modalCat"></div>
      <div class="modal-title" id="modalTitle"></div>
      <div class="modal-price" id="modalPrice"></div>
      <div class="modal-unit"  id="modalUnit"></div>
      <div class="modal-desc"  id="modalDesc"></div>
      <div class="modal-brands-title">✦ Available Brands</div>
      <div class="modal-brands-grid" id="modalBrands"></div>
      <div class="modal-features">
        <h4>Key Features</h4>
        <div id="modalFeatures"></div>
      </div>
      <a href="register.php" class="modal-btn">Order Now — Create Account →</a>
    </div>
  </div>
</div>

<script>
// Particles
const canvas = document.getElementById('particles');
const ctx    = canvas.getContext('2d');
canvas.width  = window.innerWidth;
canvas.height = window.innerHeight;
window.addEventListener('resize', () => { canvas.width=window.innerWidth; canvas.height=window.innerHeight; });

const particles = [];
for (let i = 0; i < 100; i++) {
    particles.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        r: Math.random() * 1.5 + 0.3,
        dx: (Math.random()-0.5)*0.4,
        dy: (Math.random()-0.5)*0.4,
        o: Math.random()*0.35+0.05,
        gold: Math.random() > 0.6
    });
}
function drawParticles() {
    ctx.clearRect(0,0,canvas.width,canvas.height);
    particles.forEach(p => {
        ctx.beginPath();
        ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
        ctx.fillStyle = p.gold ? `rgba(243,156,18,${p.o})` : `rgba(192,57,43,${p.o})`;
        ctx.fill();
        p.x+=p.dx; p.y+=p.dy;
        if(p.x<0||p.x>canvas.width)  p.dx*=-1;
        if(p.y<0||p.y>canvas.height) p.dy*=-1;
    });
    requestAnimationFrame(drawParticles);
}
drawParticles();

// Navbar
window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY>50);
});

// Reveal
const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
}, {threshold:0.1});
document.querySelectorAll('.reveal,.reveal-left,.reveal-right').forEach(el => observer.observe(el));

// Counter
function animateCounter(el) {
    const target = parseInt(el.dataset.target);
    if(target===0){el.textContent='0';return;}
    const step = target/(2000/16);
    let current=0;
    const timer=setInterval(()=>{
        current+=step;
        if(current>=target){current=target;clearInterval(timer);}
        el.textContent=Math.floor(current)+(target>=100?'+':target>5?'+':'');
    },16);
}
const statsObs=new IntersectionObserver((entries)=>{
    entries.forEach(e=>{
        if(e.isIntersecting){
            document.querySelectorAll('.num').forEach(animateCounter);
            statsObs.disconnect();
        }
    });
},{threshold:0.5});
const statsEl=document.querySelector('.stats');
if(statsEl) statsObs.observe(statsEl);

// Filter
function filterProducts(cat,btn) {
    document.querySelectorAll('.cat-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.product-card').forEach(card=>{
        if(cat==='all'||card.dataset.cat===cat){
            card.style.display='block';
            card.style.animation='fadeUp 0.4s ease forwards';
        } else { card.style.display='none'; }
    });
}

// Product Data
const productData = {
    cement:{cat:'Cement',title:'Ordinary Portland Cement 50kg',price:'Rs. 950',unit:'per Bag',
        desc:'High strength Ordinary Portland Cement suitable for all types of construction work including foundations, columns, beams and general masonry. Available in 50kg bags from Pakistan\'s leading cement manufacturers.',
        img:'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?w=800&q=80',
        brands:['Lucky Cement','DG Khan Cement','Maple Leaf Cement','Bestway Cement','Fauji Cement'],
        features:['High compressive strength','Suitable for all weather','ISO certified quality','Available in bulk orders','Fast setting time']},
    steel:{cat:'Steel',title:'Steel Rod 12mm — High Tensile',price:'Rs. 95,000',unit:'per Ton',
        desc:'Grade 60 high tensile deformed steel bars for reinforced concrete construction. Meets PSQCA requirements. Available in various diameters from 8mm to 32mm.',
        img:'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&q=80',
        brands:['Ittefaq Steel','Amreli Steels','Mughal Steel','Agha Steel','FF Steel'],
        features:['Grade 60 high tensile','PSQCA certified','Available 8mm to 32mm','Corrosion resistant','Bulk discounts available']},
    bricks:{cat:'Bricks',title:'Red Brick Standard — Grade A',price:'Rs. 12',unit:'per Piece',
        desc:'Premium quality kiln-fired red bricks for walls, foundations and general masonry. Grade A bricks with high compressive strength and low water absorption.',
        img:'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80',
        brands:['Shabbir Bricks Factory','Multan Brick Works','Al-Rehman Bricks','Standard Bricks'],
        features:['Kiln fired Grade A','High compressive strength','Low water absorption','Uniform size and color','Bulk delivery available']},
    sand:{cat:'Sand & Gravel',title:'Crush Sand — Fine Grade',price:'Rs. 8,000',unit:'per Trolley',
        desc:'Clean crushed sand ideal for concrete mixing, plastering and block making. Washed and screened to remove impurities.',
        img:'https://images.unsplash.com/photo-1582557280985-64e1ecfd2c56?w=800&q=80',
        brands:['Local Quarries','Chenab River Sand','Indus Crush'],
        features:['Washed and screened','No clay or silt','Ideal for concrete','Coarse and fine grades','Bulk trolley delivery']},
    pipes:{cat:'Pipes & Fittings',title:'PVC Pipe 4 inch — Heavy Duty',price:'Rs. 450',unit:'per Piece',
        desc:'Heavy duty uPVC pipes for underground drainage, sewerage and plumbing systems. Available in sizes from 0.5 inch to 12 inch.',
        img:'https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=800&q=80',
        brands:['Supreme Pipes','Master Pipes','Dadex Pipes','Diamond Pipes'],
        features:['uPVC heavy duty','Sizes 0.5" to 12"','Underground rated','Complete fittings range','Long service life']},
    paint:{cat:'Paint & Chemical',title:'Weather Shield Paint 20L',price:'Rs. 3,500',unit:'per Tin',
        desc:'Premium quality weather resistant exterior wall paint. Available in 200+ colors. Interior paints, primers and waterproofing solutions also available.',
        img:'https://images.unsplash.com/photo-1562259949-e8e7689d7828?w=800&q=80',
        brands:['Berger Paints','ICI Dulux','Nippon Paint','Master Paints','Brighto Paints'],
        features:['Weather resistant formula','200+ color options','UV protection','Washable surface','Interior & exterior grades']}
};

function openProduct(key) {
    const d=productData[key];
    document.getElementById('modalCat').textContent    = d.cat;
    document.getElementById('modalTitle').textContent  = d.title;
    document.getElementById('modalPrice').textContent  = d.price;
    document.getElementById('modalUnit').textContent   = d.unit;
    document.getElementById('modalDesc').textContent   = d.desc;
    document.getElementById('modalImg').src            = d.img;
    document.getElementById('modalBrands').innerHTML   = d.brands.map(b=>`<span class="modal-brand">${b}</span>`).join('');
    document.getElementById('modalFeatures').innerHTML = d.features.map(f=>`<div class="modal-feature">${f}</div>`).join('');
    document.getElementById('productModal').classList.add('open');
    document.body.style.overflow='hidden';
}
function closeModal(e) { if(e.target===document.getElementById('productModal')) closeProductModal(); }
function closeProductModal() { document.getElementById('productModal').classList.remove('open'); document.body.style.overflow=''; }
document.addEventListener('keydown',e=>{ if(e.key==='Escape') closeProductModal(); });
</script>
</body>
</html>