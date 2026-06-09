@props([
    'room',
])

@php
    $detailUrl = route('rooms.show', ['room' => $room->nomor_kamar]);

    $hargaHarian  = 'Rp ' . number_format((float) $room->harga_harian,  0, ',', '.') . 'k';
    // Format: jika >= 1000, singkat ke "k"
    $hHarian  = (float) $room->harga_harian;
    $hBulanan = (float) $room->harga_bulanan;
    $hargaHarianFmt  = $hHarian  >= 1000
        ? 'Rp ' . number_format($hHarian  / 1000, 0, ',', '.') . 'k'
        : 'Rp ' . number_format($hHarian,  0, ',', '.');
    $hargaBulananFmt = $hBulanan >= 1000000
        ? 'Rp ' . number_format($hBulanan / 1000000, 1, ',', '.') . 'jt'
        : 'Rp ' . number_format($hBulanan, 0, ',', '.');

    $displayStatus = (string) ($room->display_status ?? ($room->status->value ?? $room->status));

    $statusConfig = match($displayStatus) {
        'tersedia'  => ['label' => 'Tersedia',   'class' => 'room-badge--available'],
        'terisi'    => ['label' => 'Terisi',      'class' => 'room-badge--occupied'],
        'perbaikan' => ['label' => 'Perbaikan',   'class' => 'room-badge--repair'],
        default     => ['label' => 'Tidak Diketahui', 'class' => ''],
    };

    $averageRating = round((float) ($room->reviews_avg_rating ?? 0), 1);
    $reviewsCount = (int) ($room->reviews_count ?? 0);
    $fotoUtamaUrl = $room->foto_utama_url;
@endphp

<article class="room-card {{ $displayStatus === 'tersedia' ? 'room-card--available' : '' }} reveal">

    {{-- Gambar --}}
    <div class="room-card__img">
        <a href="{{ $detailUrl }}" class="room-card__media-link" aria-label="Lihat detail kamar {{ $room->nomor_kamar }}">
            @if ($fotoUtamaUrl)
                <img
                    src="{{ $fotoUtamaUrl }}"
                    alt="Foto Kamar {{ $room->nomor_kamar }}"
                    loading="lazy"
                    onerror="this.outerHTML = this.dataset.placeholder"
                    data-placeholder='<div class="room-card__placeholder"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>'
                >
            @else
                <div class="room-card__placeholder">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                </div>
            @endif
        </a>

        {{-- Status Badge Neon --}}
        <span class="room-badge {{ $statusConfig['class'] }}">
            {{ $statusConfig['label'] }}
        </span>

        {{-- Label Lantai --}}
        <span class="room-floor-tag">Lantai {{ $room->lantai }}</span>
    </div>

    {{-- Body --}}
    <div class="room-card__body">
        <div class="room-card__type">Kamar {{ $room->nomor_kamar }}</div>
        <a href="{{ $detailUrl }}" class="room-card__name">
            {{ $room->deskripsi
                ? \Illuminate\Support\Str::limit($room->deskripsi, 55)
                : 'Kamar Standar dengan kamar mandi dalam - Berlima Guest House' }}
        </a>

        <div class="room-card__rating" aria-label="Rating kamar {{ number_format($averageRating, 1, ',', '.') }} dari 5 berdasarkan {{ $reviewsCount }} ulasan">
            <span class="room-card__rating-stars" aria-hidden="true">
                @for ($star = 1; $star <= 5; $star++)
                    <span class="{{ $star <= (int) round($averageRating) ? 'is-filled' : '' }}">&#9733;</span>
                @endfor
            </span>
            <strong>{{ $reviewsCount > 0 ? number_format($averageRating, 1, ',', '.') : '0,0' }}</strong>
            <small>{{ $reviewsCount }} ulasan</small>
        </div>

        {{-- Fasilitas Chips --}}
        @if (!empty($room->fasilitas))
            <div class="room-card__amenities">
                @foreach (array_slice($room->fasilitas, 0, 4) as $item)
                    <span class="amenity-chip">
                        {{-- Icon per fasilitas --}}
                        @if (str_contains(strtolower($item), 'wifi'))
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
                        @elseif (str_contains(strtolower($item), 'ac') || str_contains(strtolower($item), 'air'))
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M1 12h2M21 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4"/></svg>
                        @elseif (str_contains(strtolower($item), 'tv'))
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        @elseif (str_contains(strtolower($item), 'parkir'))
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        @elseif (str_contains(strtolower($item), 'kamar mandi') || str_contains(strtolower($item), 'shower'))
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 12h16M4 12a8 8 0 0 1 16 0"/><path d="M12 4v4M9 5l1.5 2M15 5l-1.5 2"/></svg>
                        @elseif (str_contains(strtolower($item), 'water') || str_contains(strtolower($item), 'heater') || str_contains(strtolower($item), 'pemanas air'))
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                        @else
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        @endif
                        {{ $item }}
                    </span>
                @endforeach
                @if (count($room->fasilitas) > 4)
                    <span class="amenity-chip amenity-chip--more">+{{ count($room->fasilitas) - 4 }} lagi</span>
                @endif
            </div>
        @endif

        {{-- Footer: Harga + Tombol --}}
        <div class="room-card__footer">
            <div class="room-card__price">
                {{ $hargaHarianFmt }}
                <small>/ malam</small>
                <div class="room-card__price-monthly">{{ $hargaBulananFmt }} <small>/ bulan</small></div>
            </div>

            <a
                href="{{ $detailUrl }}"
                class="btn-wa-card"
                aria-label="Lihat detail kamar {{ $room->nomor_kamar }}"
            >
                Detail Kamar
            </a>
        </div>
    </div>
</article>
