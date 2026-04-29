    <!-- HERO GALLERY -->
    <section class="hero-gallery">
        <div class="hero-gallery__bg"></div>
        <div class="hero-gallery__content">
            <span class="section-label">Eksplorasi</span>
            <h1 class="hero-gallery__title">Galeri <span>Berlima</span></h1>
            <p class="hero-gallery__desc">Jelajahi kenyamanan dan kemewahan yang kami tawarkan. Lihat lebih dekat fasilitas dan suasana Berlima Guest House yang dirancang khusus untuk ketenangan Anda.</p>
        </div>
    </section>

    <!-- GALLERY GRID SECTION -->
    <section class="gallery">
        <div class="gallery__container">
            
            <!-- Gallery Filter (Optional UI for later functionality) -->
            <div class="gallery__filter">
                <button class="filter-btn active">Semua</button>
                <button class="filter-btn">Eksterior</button>
                <button class="filter-btn">Interior Kamar</button>
                <button class="filter-btn">Fasilitas</button>
            </div>

            <!-- Masonry/Grid -->
            <div class="gallery__grid">
                
                <!-- Item 1 -->
                <div class="gallery__item item-large">
                    <div class="gallery__img-wrapper">
                        <img src="{{ asset('images/gallery/exterior.png') }}" alt="Berlima Exterior at Dusk" class="gallery__img">
                        <div class="gallery__overlay">
                            <h3 class="gallery__title">Fasad Bangunan</h3>
                            <p class="gallery__subtitle">Suasana hangat di malam hari</p>
                        </div>
                    </div>
                </div>

                <!-- Item 2 -->
                <div class="gallery__item">
                    <div class="gallery__img-wrapper">
                        <img src="{{ asset('images/gallery/bedroom.png') }}" alt="Luxurious Bedroom" class="gallery__img">
                        <div class="gallery__overlay">
                            <h3 class="gallery__title">Kamar Premium</h3>
                            <p class="gallery__subtitle">Interior modern dan elegan</p>
                        </div>
                    </div>
                </div>

                <!-- Item 3 -->
                <div class="gallery__item item-tall">
                    <div class="gallery__img-wrapper">
                        <img src="{{ asset('images/gallery/lounge.png') }}" alt="Stylish Lounge" class="gallery__img">
                        <div class="gallery__overlay">
                            <h3 class="gallery__title">Lobby & Lounge</h3>
                            <p class="gallery__subtitle">Ruang tunggu yang nyaman</p>
                        </div>
                    </div>
                </div>

                <!-- Item 4 (Duplicate for demonstration) -->
                <div class="gallery__item">
                    <div class="gallery__img-wrapper">
                        <img src="{{ asset('images/gallery/bedroom.png') }}" alt="Deluxe Bedroom" class="gallery__img" style="filter: hue-rotate(20deg);">
                        <div class="gallery__overlay">
                            <h3 class="gallery__title">Kamar Deluxe</h3>
                            <p class="gallery__subtitle">Pencahayaan natural yang maksimal</p>
                        </div>
                    </div>
                </div>

                <!-- Item 5 (Duplicate for demonstration) -->
                <div class="gallery__item item-wide">
                    <div class="gallery__img-wrapper">
                        <img src="{{ asset('images/gallery/lounge.png') }}" alt="Dining Area" class="gallery__img" style="filter: sepia(0.2);">
                        <div class="gallery__overlay">
                            <h3 class="gallery__title">Area Bersama</h3>
                            <p class="gallery__subtitle">Desain minimalis dengan sentuhan klasik</p>
                        </div>
                    </div>
                </div>

                <!-- Item 6 (Duplicate for demonstration) -->
                <div class="gallery__item">
                    <div class="gallery__img-wrapper">
                        <img src="{{ asset('images/gallery/exterior.png') }}" alt="Garden View" class="gallery__img" style="filter: saturate(1.2);">
                        <div class="gallery__overlay">
                            <h3 class="gallery__title">Sudut Taman</h3>
                            <p class="gallery__subtitle">Kehijauan di tengah kota</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CALL TO ACTION -->
    <section class="cta" style="padding-top: 40px; padding-bottom: 100px;">
        <div class="cta__container">
            <div class="cta__content">
                <h2 class="section-title" style="margin-bottom: 10px;">Temukan Kenyamananmu</h2>
                <p class="section-sub" style="color: rgba(255,255,255,0.7);">Kamar terbatas, pastikan Anda mendapatkan yang terbaik hari ini.</p>
            </div>
            <a href="{{ route('home') }}#rooms" class="btn btn--primary" style="padding: 16px 36px; font-size: 1rem;">
                Pesan Kamar Sekarang
            </a>
        </div>
    </section>