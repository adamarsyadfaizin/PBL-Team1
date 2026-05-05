<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes" />
  <title>Tentang Berlima Guest House</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --bg: #0e0e0e;
      --bg-card: #161616;
      --accent: #4361EE;
      --orange: #F4882A;
      --text: #f0f0f0;
      --muted: #888;
      --border: rgba(255,255,255,0.07);
    }

    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'Poppins', sans-serif;
      overflow-x: hidden;
      width: 100%;
    }

    nav {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 100;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 5%;
      height: 70px;
      background: rgba(14,14,14,0.85);
      backdrop-filter: blur(14px);
      border-bottom: 1px solid var(--border);
      width: 100%;
    }

    .nav-logo {
      display: flex;
      align-items: center;
      gap: 6px;
      text-decoration: none;
      flex-shrink: 0;
    }

    .nav-logo-text {
      font-weight: 700;
      font-size: 1.3rem;
      color: var(--text);
    }

    .nav-logo-dot {
      width: 8px;
      height: 8px;
      background: var(--accent);
      border-radius: 50%;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 20px;
      list-style: none;
      flex-wrap: wrap;
      justify-content: center;
    }

    .nav-links a {
      color: var(--text);
      text-decoration: none;
      font-size: 0.85rem;
      font-weight: 500;
      opacity: 0.75;
      white-space: nowrap;
    }

    .nav-links a.active {
      opacity: 1;
      color: var(--accent);
    }

    .btn-login {
      background: var(--accent);
      color: #fff;
      border: none;
      padding: 8px 20px;
      border-radius: 8px;
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      flex-shrink: 0;
    }

    .about-hero {
      min-height: 40vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 100px 5% 40px;
      width: 100%;
      position: relative;
    }

    .about-hero::before {
      content: '';
      position: absolute;
      top: -100px;
      left: -120px;
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(67,97,238,.15) 0%, transparent 70%);
      pointer-events: none;
    }

    .about-hero-content {
      position: relative;
      z-index: 2;
    }

    .about-title {
      font-size: clamp(1.8rem, 6vw, 3rem);
      font-weight: 700;
      margin-bottom: 16px;
    }

    .about-title span {
      color: var(--accent);
    }

    .about-subtitle {
      color: var(--muted);
      font-size: 0.9rem;
      max-width: 90%;
      margin: 0 auto;
    }

    .container {
      max-width: 100%;
      padding: 0 5%;
      margin: 0 auto;
    }

    .section-label {
      display: inline-block;
      background: rgba(67,97,238,0.12);
      border: 1px solid rgba(67,97,238,0.25);
      color: var(--accent);
      font-size: 0.7rem;
      font-weight: 600;
      text-transform: uppercase;
      padding: 4px 12px;
      border-radius: 100px;
      margin-bottom: 16px;
    }

    .section-title {
      font-size: clamp(1.4rem, 5vw, 2rem);
      font-weight: 700;
      margin-bottom: 12px;
    }

    .section-sub {
      color: var(--muted);
      font-size: 0.85rem;
      line-height: 1.6;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 16px;
      margin: 40px 0;
    }

    .stat-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 20px 12px;
      text-align: center;
    }

    .stat-number {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--accent);
    }

    .stat-label {
      color: var(--muted);
      font-size: 0.75rem;
      margin-top: 6px;
    }

    .visi-misi-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 24px;
      margin: 40px 0;
    }

    @media (min-width: 768px) {
      .visi-misi-grid {
        grid-template-columns: 1fr 1fr;
      }
    }

    .vm-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 24px;
    }

    .vm-icon {
      width: 48px;
      height: 48px;
      background: rgba(67,97,238,0.15);
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 16px;
    }

    .vm-card h3 {
      font-size: 1.3rem;
      margin-bottom: 12px;
    }

    .vm-card p, .vm-card li {
      color: var(--muted);
      font-size: 0.85rem;
      line-height: 1.6;
    }

    .vm-card ul {
      list-style: none;
      padding-left: 0;
    }

    .vm-card li {
      margin-bottom: 10px;
      padding-left: 20px;
      position: relative;
    }

    .vm-card li::before {
      content: "✦";
      position: absolute;
      left: 0;
      color: var(--accent);
    }

    .fac-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 16px;
      margin: 30px 0;
    }

    .fac-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 20px 12px;
      text-align: center;
    }

    .fac-icon {
      width: 44px;
      height: 44px;
      background: rgba(67,97,238,0.15);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 12px;
      color: var(--accent);
    }

    .fac-card h4 {
      font-size: 0.9rem;
      font-weight: 600;
      margin-bottom: 6px;
    }

    .fac-card p {
      color: var(--muted);
      font-size: 0.75rem;
    }

    .team-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 20px;
      margin: 30px 0;
    }

    .team-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 20px 12px;
      text-align: center;
    }

    .team-avatar {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, #1e3c72, #2a5298);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 12px;
    }

    .team-card h5 {
      font-size: 0.9rem;
      font-weight: 600;
    }

    .team-card .role {
      color: var(--accent);
      font-size: 0.7rem;
    }

    .team-card .nim {
      color: var(--muted);
      font-size: 0.65rem;
    }

    .contact-section {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 30px 20px;
      margin: 40px 0;
    }

    .contact-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 24px;
      text-align: center;
    }

    @media (min-width: 640px) {
      .contact-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    .contact-item p {
      color: var(--muted);
      font-size: 0.8rem;
      margin-top: 8px;
      word-break: break-word;
    }

    .social-links {
      display: flex;
      justify-content: center;
      gap: 16px;
      margin-top: 30px;
      padding-top: 24px;
      border-top: 1px solid var(--border);
    }

    .social-icon {
      width: 36px;
      height: 36px;
      background: rgba(67,97,238,0.1);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    footer {
      background: #080808;
      border-top: 1px solid var(--border);
      padding: 40px 5% 30px;
      width: 100%;
    }

    .footer-bottom {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
      color: var(--muted);
      font-size: 0.7rem;
      text-align: center;
    }

    @media (min-width: 640px) {
      .footer-bottom {
        flex-direction: row;
        justify-content: space-between;
      }
    }

    .reveal {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.5s ease, transform 0.5s ease;
    }

    .reveal.visible {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
</head>
<body>

<nav>
  <a href="/" class="nav-logo">
    <span class="nav-logo-text">Berlima</span>
    <span class="nav-logo-dot"></span>
  </a>
  <ul class="nav-links">
    <li><a href="/">Home</a></li>
    <li><a href="#">Rooms</a></li>
    <li><a href="/about" class="active">About</a></li>
    <li><a href="#">Facilities</a></li>
    <li><a href="#">Testimonials</a></li>
  </ul>
  <button class="btn-login">Login</button>
</nav>

<div class="about-hero">
  <div class="about-hero-content">
    <h1 class="about-title">Tentang <span>Berlima</span> Guest House</h1>
    <p class="about-subtitle">Homey and Affordable - Tempat menginap terbaik di Malang</p>
  </div>
</div>

<main class="container">
  <div class="reveal">
    <span class="section-label">Tentang Kami</span>
    <h2 class="section-title">Kenapa Memilih Berlima?</h2>
    <p class="section-sub">Berlima Guest House didirikan pada tahun 2021 dengan visi menyediakan akomodasi berkualitas dengan harga terjangkau. Kami memiliki 35 kamar dengan satu tipe kamar Standar. Setiap kamar dilengkapi dengan fasilitas modern untuk kenyamanan tamu. Lokasi strategis di Sawojajar, Malang, dekat dengan berbagai pusat kuliner dan transportasi umum.</p>
  </div>

  <div class="stats-grid">
    <div class="stat-card reveal"><div class="stat-number">35</div><div class="stat-label">Kamar Tersedia</div></div>
    <div class="stat-card reveal"><div class="stat-number">100+</div><div class="stat-label">Tamu Puas</div></div>
    <div class="stat-card reveal"><div class="stat-number">4.3</div><div class="stat-label">Rating Google</div></div>
    <div class="stat-card reveal"><div class="stat-number">24/7</div><div class="stat-label">Layanan</div></div>
  </div>

  <div class="visi-misi-grid">
    <div class="vm-card reveal">
      <div class="vm-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4361EE" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></div>
      <h3>Visi</h3>
      <p>Menjadi guest house pilihan di Malang yang mengutamakan kenyamanan, keramahan, dan pelayanan terbaik.</p>
    </div>
    <div class="vm-card reveal">
      <div class="vm-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#F4882A" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
      <h3>Misi</h3>
      <ul>
        <li>Menyediakan akomodasi berkualitas dengan harga terjangkau</li>
        <li>Memberikan pelayanan ramah dan profesional</li>
        <li>Menjaga kebersihan dan kenyamanan fasilitas</li>
        <li>Terus berinovasi dalam meningkatkan pengalaman menginap</li>
      </ul>
    </div>
  </div>

  <div class="reveal" style="margin-top: 40px;">
    <span class="section-label">Fasilitas</span>
    <h2 class="section-title">Fasilitas Kami</h2>
  </div>
  <div class="fac-grid">
    <div class="fac-card reveal"><div class="fac-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg></div><h4>Wi-Fi</h4><p>Free 24 jam</p></div>
    <div class="fac-card reveal"><div class="fac-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/></svg></div><h4>Parkir Gratis</h4><p>Aman</p></div>
    <div class="fac-card reveal"><div class="fac-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4"/></svg></div><h4>AC</h4><p>Semua kamar</p></div>
    <div class="fac-card reveal"><div class="fac-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><h4>CCTV 24 Jam</h4><p>Keamanan terjamin</p></div>
    <div class="fac-card reveal"><div class="fac-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div><h4>Ramah Tamah</h4><p>Staff profesional</p></div>
  </div>

  <div class="reveal" style="margin-top: 60px;">
    <span class="section-label">Tim Pengembang</span>
    <h2 class="section-title">Dibalik Berlima Guest House</h2>
  </div>
  <div class="team-grid">
    @php
      $teams = [
        ['nama' => 'Ganti Nama 1', 'role' => 'Frontend About', 'nim' => 'Ganti NIM'],
        ['nama' => 'Ganti Nama 2', 'role' => 'Role Anggota 2', 'nim' => 'Ganti NIM'],
        ['nama' => 'Ganti Nama 3', 'role' => 'Role Anggota 3', 'nim' => 'Ganti NIM'],
        ['nama' => 'Ganti Nama 4', 'role' => 'Role Anggota 4', 'nim' => 'Ganti NIM'],
        ['nama' => 'Ganti Nama 5', 'role' => 'Role Anggota 5', 'nim' => 'Ganti NIM']
      ];
    @endphp
    @foreach($teams as $team)
    <div class="team-card reveal">
      <div class="team-avatar">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1">
          <circle cx="12" cy="8" r="4"/>
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
        </svg>
      </div>
      <h5>{{ $team['nama'] }}</h5>
      <div class="role">{{ $team['role'] }}</div>
      <div class="nim">{{ $team['nim'] }}</div>
    </div>
    @endforeach
  </div>

  <div class="contact-section reveal">
    <div class="contact-grid">
      <div class="contact-item">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4361EE" stroke-width="2">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
          <circle cx="12" cy="10" r="3"/>
        </svg>
        <p>Kav 4, Jalan Terusan Wisnu Wardana Kav.4, Sawojajar, Sekarpuro, Wiagaro, Kabupaten Malang, Jawa Timur 65157</p>
      </div>
      <div class="contact-item">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4361EE" stroke-width="2">
          <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.574 2.81.7A2 2 0 0 1 22 16.92z"/>
        </svg>
        <p>(021) 29707601</p>
      </div>
      <div class="contact-item">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4361EE" stroke-width="2">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
          <polyline points="22,6 12,13 2,6"/>
        </svg>
        <p>info@berlimagh.com</p>
      </div>
    </div>
    <div class="social-links">
      <a href="#" class="social-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/></svg></a>
      <a href="#" class="social-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg></a>
    </div>
  </div>
</main>

<footer>
  <div class="footer-bottom">
    <span>2025 Berlima Guest House. All rights reserved.</span>
    <span>Made in Indonesia</span>
  </div>
</footer>

<script>
  const reveals = document.querySelectorAll('.reveal');
  const observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry, i) {
      if (entry.isIntersecting) {
        setTimeout(function() {
          entry.target.classList.add('visible');
        }, i * 60);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });
  reveals.forEach(function(el) {
    observer.observe(el);
  });
</script>

</body>
</html>