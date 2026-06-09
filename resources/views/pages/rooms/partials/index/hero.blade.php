<section class="rooms-hero">
    <div class="rooms-hero__glow rooms-hero__glow--left" aria-hidden="true"></div>
    <div class="rooms-hero__glow rooms-hero__glow--right" aria-hidden="true"></div>
    <div class="rooms-hero__inner">
        <span class="section-label">🛏 Kamar & Hunian</span>
        <h1 class="rooms-hero__title">
            Temukan Kamar<br>
            <span class="rooms-hero__accent">Berlima Guest House</span>
        </h1>
        <p class="rooms-hero__sub">
            Hunian nyaman dengan fasilitas lengkap untuk kebutuhan harian dan bulanan.<br>
            Semua kamar dilengkapi AC, WiFi, dan kamar mandi dalam.
        </p>

        <div class="rooms-hero__stats">
            <div class="rooms-stat">
                <span class="rooms-stat__val">{{ $totalRooms }}</span>
                <span class="rooms-stat__label">Kamar Tersedia</span>
            </div>
            <div class="rooms-stat-divider"></div>
            <div class="rooms-stat">
                <span class="rooms-stat__val">
                    {{ $availableRooms }}
                </span>
                <span class="rooms-stat__label">Bisa Dipesan</span>
            </div>
            <div class="rooms-stat-divider"></div>
            <div class="rooms-stat">
                <span class="rooms-stat__val">24/7</span>
                <span class="rooms-stat__label">Layanan</span>
            </div>
        </div>
    </div>
</section>
