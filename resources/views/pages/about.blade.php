<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tentang Berlima Guest House</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
 * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --bg: #0e0e0e;
    --bg-card: #161616;
    --accent: #4361EE;
    --text: #f0f0f0;
    --muted: #888;
    --border: rgba(255, 255, 255, 0.07);
}

body {
    background: var(--bg);
    color: var(--text);
    font-family: 'Poppins', sans-serif;
}

nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 5%;
    height: 70px;
    background: rgba(14, 14, 14, 0.85);
    backdrop-filter: blur(14px);
    border-bottom: 1px solid var(--border);
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
    display: inline-block;
}

.nav-links {
    display: flex;
    gap: 20px;
    list-style: none;
}

.nav-links a {
    color: var(--text);
    text-decoration: none;
    font-size: 0.85rem;
    opacity: 0.75;
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
    cursor: pointer;
}

.about-hero {
    min-height: 40vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 100px 5% 40px;
}

.about-title {
    font-size: clamp(1.8rem, 6vw, 3rem);
    font-weight: 700;
}

.about-title span {
    color: var(--accent);
}

.container {
    padding: 0 5%;
}

.section-label {
    display: inline-block;
    background: rgba(67, 97, 238, 0.12);
    border: 1px solid rgba(67, 97, 238, 0.25);
    color: var(--accent);
    font-size: 0.7rem;
    padding: 4px 12px;
    border-radius: 100px;
    margin-bottom: 16px;
}

.section-title {
    font-size: clamp(1.4rem, 5vw, 2rem);
    font-weight: 700;
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
    padding: 20px;
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
    padding: 20px;
    text-align: center;
}

.fac-icon {
    width: 52px;
    height: 52px;
    background: rgba(67, 97, 238, 0.12);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    color: var(--accent);
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

footer {
    background: #080808;
    border-top: 1px solid var(--border);
    padding: 40px 5%;
}

.footer-bottom {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    color: var(--muted);
    font-size: 0.7rem;
}

.reveal {
    opacity: 0;
    transform: translateY(20px);
    transition: 0.5s;
}

.reveal.visible {
    opacity: 1;
    transform: translateY(0);
}
  </style>
</head>
<body>

<nav>
  <a href="/" style="text-decoration:none"><span class="nav-logo-text">Berlima</span><span class="nav-logo-dot"></span></a>
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
    <h1 class="about-title">Tentang <span>Berlima</span></h1>
    <p class="about-subtitle">Guest house nyaman di pusat Kota Malang dengan harga bersahabat.</p>
  </div>
</div>

<main class="container">
  <div class="reveal">
    <span class="section-label">Tentang Kami</span>
    <h2 class="section-title">Mengapa Memilih Berlima Guest House?</h2>
    <p>Berlima Guest House berdiri sejak tahun 2021 dengan komitmen menyediakan tempat menginap yang nyaman, bersih, dan terjangkau bagi masyarakat. 
      Kami memiliki 35 kamar tipe Standar yang dilengkapi berbagai fasilitas untuk menunjang kenyamanan tamu selama menginap. 
      Berlokasi strategis di kawasan Sawojajar, Kota Malang. Berlima Guest House mudah dijangkau dari pusat kuliner, kawasan pendidikan, pusat perbelanjaan, maupun sarana transportasi umum.</p>
  </div>

  <div class="stats-grid">
    <div class="stat-card"><div class="stat-number">35</div><div class="stat-label">Kamar</div></div>
    <div class="stat-card"><div class="stat-number">100+</div><div class="stat-label">Tamu Puas</div></div>
    <div class="stat-card"><div class="stat-number">4.3</div><div class="stat-label">Penilaian Tamu</div></div>
    <div class="stat-card"><div class="stat-number">24/7</div><div class="stat-label">Layanan</div></div>
  </div>

  <div class="visi-misi-grid">
    <div class="vm-card"><h3>Visi</h3><p>Menjadi penginapan pilihan di Kota Malang yang mengutamakan kenyamanan, keramahan, dan pelayanan terbaik bagi setiap tamu.</p></div>
    <div class="vm-card"><h3>Misi</h3><ul><li>Tempat menginap berkualitas dengan harga terjangkau</li><li>Memberikan pelayanan ramah, cepat, dan profesional</li><li>Menjaga kebersihan serta kenyamanan</li><li>Terus belajar dan berinovasi untuk meningkatkan pengalaman tamu</li></ul></div>
  </div>

  <div><span class="section-label">Fasilitas</span><h2 class="section-title">Fasilitas Kami</h2></div>
  <div class="fac-grid">
    <div class="fac-card">
      <div class="fac-icon">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M5 12.55a11 11 0 0 1 14.08 0"/>
          <line x1="12" y1="20" x2="12.01" y2="20"/>
        </svg>
      </div>
      <h4>Wi-Fi</h4>
      <p>Akses internet gratis selama 24 jam</p>
    </div>
    <div class="fac-card">
      <div class="fac-icon">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polygon points="12 2 2 7 12 12 22 7 12 2"/>
          <polyline points="2 17 12 22 22 17"/>
          <polyline points="2 12 12 17 22 12"/>
        </svg>
      </div>
      <h4>Area Parkir Gratis</h4>
      <p>Area parkir luas dan aman</p>
    </div>
    <div class="fac-card">
      <div class="fac-icon">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="5"/>
          <path d="M12 1v2M12 21v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4"/>
        </svg>
      </div>
      <h4>Pendingin Ruangan (AC)</h4>
      <p>Tersedia di setiap kamar</p>
    </div>
    <div class="fac-card">
      <div class="fac-icon">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
      </div>
      <h4>CCTV 24 Jam</h4>
      <p>Sistem keamanan aktif sepanjang waktu</p>
    </div>
    <div class="fac-card">
      <div class="fac-icon">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
      </div>
      <h4>Pelayanan ramah</h4>
      <p>Dilayani oleh staff yang profesional dan bersahabat</p>
    </div>
  </div>

  <div class="contact-section">
    <div class="contact-grid">
      <div><p>Alamat
        Kav 4, Jalan Terusan Wisnu Wardana, Sawojajar, Malang</p></div>
      <div><p>Telepon 
        (021) 29707601</p></div>
      <div><p>info@berlimaguesthouse.com</p></div>
    </div>
  </div>
</main>

<footer><div class="footer-bottom"><span>2025 Berlima Guest House. Seluruh hak cipta dilindungi</span><span>Dibuat di Indonesia</span></div></footer>

<script>
  const observer=new IntersectionObserver(e=>e.forEach(e=>e.isIntersecting&&(e.target.classList.add('visible'),observer.unobserve(e.target))),{threshold:.1});
  document.querySelectorAll('.reveal').forEach(el=>observer.observe(el));
</script>
</body>
</html>