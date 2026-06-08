@extends('layouts.app')

@section('title', 'Tentang Berlima Guest House')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/about.css') }}">
@endpush

@section('content')
<div class="about-hero">
  <div class="about-hero-content">
    <h1 class="about-title">Tentang <span>Berlima</span></h1>
    <p class="about-subtitle">Guest house nyaman di pusat Kota Malang dengan harga bersahabat.</p>
  </div>
</div>

<div class="container">
  <div class="reveal">
    <span class="section-label">Tentang Kami</span>
    <h2 class="section-title">Mengapa Memilih Berlima Guest House?</h2>
    <p>Berlima Guest House berdiri sejak tahun 2021 dengan komitmen menyediakan tempat menginap yang nyaman, bersih, dan terjangkau bagi masyarakat. 
      Kami memiliki 35 kamar tipe Standar yang dilengkapi berbagai fasilitas untuk menunjang kenyamanan tamu selama menginap. 
      Berlokasi strategis di kawasan Sawojajar, Kota Malang. Berlima Guest House mudah dijangkau dari pusat kuliner, kawasan pendidikan, pusat perbelanjaan, maupun sarana transportasi umum.</p>
  </div>

  <div class="stats-grid reveal">
    <div class="stat-card"><div class="stat-number">35</div><div class="stat-label">Kamar</div></div>
    <div class="stat-card"><div class="stat-number">100+</div><div class="stat-label">Tamu Puas</div></div>
    <div class="stat-card"><div class="stat-number">4.3</div><div class="stat-label">Penilaian Tamu</div></div>
    <div class="stat-card"><div class="stat-number">24/7</div><div class="stat-label">Layanan</div></div>
  </div>

  <div class="visi-misi-grid reveal">
    <div class="vm-card"><h3>Visi</h3><br><p>Menjadi penginapan pilihan di Kota Malang yang mengutamakan kenyamanan, keramahan, dan pelayanan terbaik bagi setiap tamu.</p></div>
    <div class="vm-card"><h3>Misi</h3><br><ul style="padding-left: 20px;"><li>Tempat menginap berkualitas dengan harga terjangkau</li><li>Memberikan pelayanan ramah, cepat, dan profesional</li><li>Menjaga kebersihan serta kenyamanan</li><li>Terus belajar dan berinovasi untuk meningkatkan pengalaman tamu</li></ul></div>
  </div>

  <div class="reveal"><span class="section-label">Fasilitas</span><h2 class="section-title">Fasilitas Kami</h2></div>
  <div class="fac-grid reveal">
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

  <div class="contact-section reveal">
    <div class="contact-grid">
      <div><p>Alamat<br>Kav 4, Jalan Terusan Wisnu Wardana, Sawojajar, Malang</p></div>
      <div><p>Telepon<br>(021) 29707601</p></div>
      <div><p>Email<br>info@berlimaguesthouse.com</p></div>
    </div>
  </div>
</div>
@endsection