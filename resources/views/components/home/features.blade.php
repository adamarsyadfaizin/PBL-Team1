{{-- ─── ROOMS PREVIEW ──────────────────────────────────────── --}}
<section class="rooms" id="rooms">
  <div class="rooms-head reveal">
    <div>
      <span class="section-label">🛏 Kamar Pilihan</span>
      <h2 class="section-title">Hunian Nyaman<br>untuk Semua Kebutuhan</h2>
      <p class="section-sub" style="margin-top:12px">
        Pilih kamar sesuai durasi dan kebutuhan kamu — harian, mingguan, atau bulanan.<br>
        Semua dilengkapi fasilitas modern di lingkungan yang aman dan tenang.
      </p>
    </div>
    <a href="{{ route('rooms.index') }}" class="btn-outline" id="btn-lihat-semua-kamar">
      Lihat Semua Kamar
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <line x1="5" y1="12" x2="19" y2="12"/>
        <polyline points="12 5 19 12 12 19"/>
      </svg>
    </a>
  </div>

  {{-- Preview 3 Keunggulan Kamar (bukan listing kamar) --}}
  <div class="rooms-grid">

    {{-- Keunggulan 1 --}}
    <div class="room-card reveal">
      <div class="room-img">
        <div class="room-img-placeholder deluxe">
          <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1" opacity=".2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
          </svg>
        </div>
        <span class="room-tag best">✦ Harian</span>
      </div>
      <div class="room-body">
        <div class="room-type">Sewa Harian</div>
        <div class="room-name">Fleksibel untuk perjalanan singkat</div>
        <div class="room-amenities">
          <div class="amenity-chip">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/>
              <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/>
            </svg>
            WiFi
          </div>
          <div class="amenity-chip">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M1 12h2M21 12h2"/>
            </svg>
            AC
          </div>
          <div class="amenity-chip">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
            </svg>
            Water Heater
          </div>
        </div>
        <div class="room-footer">
          <div class="room-price">Mulai <span style="color:#4361EE">Rp 150k</span> <small>/ malam</small></div>
          <a href="{{ route('rooms.index') }}" class="btn-book">Cek Kamar</a>
        </div>
      </div>
    </div>

    {{-- Keunggulan 2 --}}
    <div class="room-card featured reveal">
      <div class="room-img">
        <div class="room-img-placeholder suite">
          <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1" opacity=".2">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
          </svg>
        </div>
        <span class="room-tag popular">⭐ Terfavorit</span>
      </div>
      <div class="room-body">
        <div class="room-type">Sewa Bulanan</div>
        <div class="room-name">Hemat untuk kos jangka panjang</div>
        <div class="room-amenities">
          <div class="amenity-chip">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/>
              <line x1="12" y1="17" x2="12" y2="21"/>
            </svg>
            TV
          </div>
          <div class="amenity-chip">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M5 12.55a11 11 0 0 1 14.08 0"/><line x1="12" y1="20" x2="12.01" y2="20"/>
            </svg>
            WiFi
          </div>
          <div class="amenity-chip">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/>
              <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
            Parkir
          </div>
        </div>
        <div class="room-footer">
          <div class="room-price">Mulai <span style="color:#4361EE">Rp 2,5jt</span> <small>/ bulan</small></div>
          <a href="{{ route('rooms.index') }}" class="btn-book">Cek Kamar</a>
        </div>
      </div>
    </div>

    {{-- Keunggulan 3 --}}
    <div class="room-card reveal">
      <div class="room-img">
        <div class="room-img-placeholder standard">
          <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1" opacity=".2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
          </svg>
        </div>
        <span class="room-tag">🔒 Aman</span>
      </div>
      <div class="room-body">
        <div class="room-type">Keamanan 24/7</div>
        <div class="room-name">Lingkungan aman, tenang dan bersih</div>
        <div class="room-amenities">
          <div class="amenity-chip">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            CCTV 24H
          </div>
          <div class="amenity-chip">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/>
            </svg>
            Check-in Fleksibel
          </div>
        </div>
        <div class="room-footer">
          <div class="room-price"><span style="color:#4ade80">Tersedia</span> Sekarang</div>
          <a href="{{ route('rooms.index') }}" class="btn-book">Cek Kamar</a>
        </div>
      </div>
    </div>

  </div>
</section>

{{-- ─── FASILITAS UNGGULAN ─────────────────────────────────── --}}
<section class="facilities" id="facilities">
  <div class="facilities-inner">
    <div class="facilities-head reveal">
      <div>
        <span class="section-label">✨ Fasilitas Unggulan</span>
        <h2 class="section-title">Kenyamanan Anda<br>Adalah Prioritas</h2>
      </div>
    </div>

    <div class="fac-grid">
      <div class="fac-card reveal">
        <div class="fac-icon blue">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12.55a11 11 0 0 1 14.08 0"/>
            <path d="M1.42 9a16 16 0 0 1 21.16 0"/>
            <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
            <line x1="12" y1="20" x2="12.01" y2="20"/>
          </svg>
        </div>
        <h4>WiFi Kencang</h4>
        <p>Akses internet gratis berkecepatan tinggi di seluruh area guest house.</p>
      </div>
      <div class="fac-card reveal">
        <div class="fac-icon orange">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
            <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
            <line x1="6" y1="1" x2="6" y2="4"/>
            <line x1="10" y1="1" x2="10" y2="4"/>
            <line x1="14" y1="1" x2="14" y2="4"/>
          </svg>
        </div>
        <h4>Sarapan Gratis</h4>
        <p>Nikmati hidangan sarapan lezat setiap pagi selama Anda menginap.</p>
      </div>
      <div class="fac-card reveal">
        <div class="fac-icon yellow">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="5"/>
            <path d="M12 1v2M12 21v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M1 12h2M21 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4"/>
          </svg>
        </div>
        <h4>AC &amp; Water Heater</h4>
        <p>Pengatur suhu ruangan dan pemanas air di setiap kamar.</p>
      </div>
      <div class="fac-card reveal">
        <div class="fac-icon red">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
          </svg>
        </div>
        <h4>Area Parkir Luas</h4>
        <p>Tempat parkir gratis untuk tamu dengan kapasitas memadai.</p>
      </div>
    </div>
  </div>
</section>