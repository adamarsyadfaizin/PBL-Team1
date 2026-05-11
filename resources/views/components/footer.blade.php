<!-- footer utama, sempet bingung mau bagi sectionnya gimana tapi akhirnya pakai pattern ini aja --> 
 <footer class="footer"> <div class="footer__inner">
        <div class="footer__top">

        <!-- bagian brand / logo -->
        <div class="footer__brand">
            <a href="/" class="footer__brand-logo">
                <!-- nama brand, mungkin nanti bisa diganti pakai img kalau ada logo asli -->
                <span class="footer__brand-name">Berlima</span>
                <span class="footer__brand-dot"></span> <!-- titik kecil biar ada aksen -->
            </a>

            <!-- deskripsi singkat, ini masih generic sih -->
            <p class="footer__desc">
                Hunian nyaman dengan fasilitas lengkap dan lokasi strategis. 
                Solusi terbaik untuk perjalanan bisnis maupun liburan Anda.
            </p>

            <!-- social media icons -->
            <div class="footer__socials">

                <!-- Instagram -->
                <a href="https://instagram.com" target="_blank" class="footer__social-btn" aria-label="Instagram">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="2" width="20" height="20" rx="5"/>
                        <circle cx="12" cy="12" r="4"/>
                        <circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>
                    </svg>
                </a>

                <!-- WhatsApp (nomor dummy, nanti ganti aja) -->
                <a href="https://wa.me/6281234567890" target="_blank" class="footer__social-btn" aria-label="WhatsApp">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                    </svg>
                </a>

                <!-- Email -->
                <a href="mailto:info@berlimaguesthouse.com" class="footer__social-btn" aria-label="Email">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </a>

                <!-- sempet kepikiran nambah facebook, tapi belum perlu -->
            </div>
        </div>

        <!-- kolom kamar -->
        <div class="footer__col">
            <h5 class="footer__col-title">Kamar</h5>
            <ul class="footer__col-list">
                <li><a href="#" class="footer__col-link">Standard Room</a></li>
                <li><a href="#" class="footer__col-link">Deluxe Room</a></li>
                <li><a href="#" class="footer__col-link">Suite Room</a></li>
                <li><a href="#" class="footer__col-link">Family Room</a></li>
            </ul>
        </div>

        <!-- info umum -->
        <div class="footer__col">
            <h5 class="footer__col-title">Informasi</h5>
            <ul class="footer__col-list">
                <!-- link masih dummy semua -->
                <li><a href="#" class="footer__col-link">Tentang Kami</a></li>
                <li><a href="#" class="footer__col-link">Fasilitas</a></li>
                <li><a href="#" class="footer__col-link">Lokasi</a></li>
                <li><a href="#" class="footer__col-link">Kontak</a></li>
            </ul>
        </div>

        <!-- bantuan / support -->
        <div class="footer__col">
            <h5 class="footer__col-title">Bantuan</h5>
            <ul class="footer__col-list">
                <li><a href="#" class="footer__col-link">Cara Booking</a></li>
                <li><a href="#" class="footer__col-link">Kebijakan Refund</a></li>
                <li><a href="#" class="footer__col-link">FAQ</a></li>
                <li><a href="#" class="footer__col-link">Syarat & Ketentuan</a></li>
            </ul>
        </div>

    </div>

    <!-- bagian bawah footer -->
    <div class="footer__bottom">
        <!-- tahun hardcoded dulu, nanti mungkin pakai JS biar auto update -->
        <span>© 2025 Berlima Guest House. All rights reserved.</span>

        <!-- kecil tapi penting 😄 -->
        <span>Made with ❤️ in Indonesia</span>
    </div>

</div> 
</footer>