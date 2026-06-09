@php
    $guestProfile = \App\Models\GuestProfile::active();
    $contactItems = collect($guestProfile->contact_items ?: []);
    $whatsappItem = $contactItems->first(
        fn (array $item): bool => str_contains(strtolower((string) ($item['label'] ?? '')), 'whatsapp')
    );
    $emailItem = $contactItems->first(
        fn (array $item): bool => str_contains(strtolower((string) ($item['label'] ?? '')), 'email')
    );
    $hoursItem = $contactItems->first(
        fn (array $item): bool => str_contains(strtolower((string) ($item['label'] ?? '')), 'operasional')
    );
    $checkItem = $contactItems->first(
        fn (array $item): bool => str_contains(strtolower((string) ($item['label'] ?? '')), 'check-in')
    );
    $whatsappUrl = $guestProfile->whatsappUrl('Halo Berlima Guest House, saya ingin bertanya tentang ketersediaan kamar.');
@endphp

<footer class="footer">
    <div class="footer__inner">
        <div class="footer__top">
            <div class="footer__brand">
                <a href="{{ route('home') }}" class="footer__brand-logo" aria-label="Berlima Guest House">
                    <span class="footer__brand-name">Berlima Guest House</span>
                    <span class="footer__brand-dot" aria-hidden="true"></span>
                </a>

                <p class="footer__desc">
                    Cari kamar, cek detail harga dan ketersediaan, lalu kirim reservasi untuk dikonfirmasi admin.
                </p>

                <div class="footer__actions">
                    <a href="{{ route('rooms.index') }}" class="footer__action footer__action--primary">Cari kamar</a>
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="footer__action">WhatsApp admin</a>
                </div>
            </div>

            <div class="footer__col">
                <h5 class="footer__col-title">Reservasi</h5>
                <ul class="footer__col-list">
                    <li><a href="{{ route('rooms.index') }}" class="footer__col-link">Daftar kamar</a></li>
                    <li><a href="{{ route('rooms.index', ['status' => 'tersedia']) }}" class="footer__col-link">Kamar tersedia</a></li>
                    <li><a href="{{ route('contact') }}#guest-request-form" class="footer__col-link">Tanya kebutuhan menginap</a></li>
                    @auth
                        <li><a href="{{ route('profile') }}" class="footer__col-link">Profil penyewa</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="footer__col-link">Login penyewa</a></li>
                    @endauth
                </ul>
            </div>

            <div class="footer__col">
                <h5 class="footer__col-title">Informasi</h5>
                <ul class="footer__col-list">
                    <li><a href="{{ route('about') }}" class="footer__col-link">Tentang Berlima</a></li>
                    <li><a href="{{ route('about') }}#about-gallery" class="footer__col-link">Galeri guest house</a></li>
                    <li><a href="{{ route('contact') }}" class="footer__col-link">Kontak dan FAQ</a></li>
                    <li><a href="{{ route('contact') }}#map-title" class="footer__col-link">Lokasi</a></li>
                </ul>
            </div>

            <div class="footer__col footer__col--process">
                <h5 class="footer__col-title">Alur sistem</h5>
                <ul class="footer__steps">
                    <li>Detail kamar menampilkan foto, fasilitas, harga, status, dan ulasan.</li>
                    <li>Form booking menghitung durasi, total tagihan, dan meminta bukti transfer.</li>
                    <li>Admin mengecek data reservasi lalu menghubungi penyewa melalui WhatsApp.</li>
                </ul>
            </div>
        </div>

        <div class="footer__contact">
            @if ($whatsappItem)
                <a href="{{ $whatsappItem['url'] ?? $whatsappUrl }}" target="_blank" rel="noopener" class="footer__contact-item">
                    <span>{{ $whatsappItem['label'] ?? 'WhatsApp Admin' }}</span>
                    <strong>{{ $whatsappItem['value'] ?? 'Hubungi admin' }}</strong>
                </a>
            @endif

            @if ($emailItem)
                <a href="{{ $emailItem['url'] ?? 'mailto:' . ($emailItem['value'] ?? '') }}" class="footer__contact-item">
                    <span>{{ $emailItem['label'] ?? 'Email' }}</span>
                    <strong>{{ $emailItem['value'] ?? 'info@berlimaguesthouse.com' }}</strong>
                </a>
            @endif

            @if ($hoursItem)
                <div class="footer__contact-item">
                    <span>{{ $hoursItem['label'] ?? 'Jam Operasional' }}</span>
                    <strong>{{ $hoursItem['value'] }}</strong>
                </div>
            @endif

            @if ($checkItem)
                <div class="footer__contact-item">
                    <span>{{ $checkItem['label'] ?? 'Check-in / Check-out' }}</span>
                    <strong>{{ $checkItem['value'] }}</strong>
                </div>
            @endif
        </div>

        <div class="footer__bottom">
            <span>&copy; {{ now()->year }} Berlima Guest House</span>
            <span>Reservasi dicatat sistem, konfirmasi tetap melalui admin.</span>
        </div>
    </div>
</footer>
