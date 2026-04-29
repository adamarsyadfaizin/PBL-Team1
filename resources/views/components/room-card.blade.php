@props([
    'room',
    'waNumber' => '6281234567890',
])

@php
    $waText = urlencode("Halo Berlima House, saya ingin memesan Kamar {$room->nomor_kamar}. Apakah masih tersedia?");
    $waUrl  = "https://wa.me/{$waNumber}?text={$waText}";

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

    $statusConfig = match($room->status->value ?? $room->status) {
        'tersedia'  => ['label' => 'Tersedia',   'class' => 'room-badge--available'],
        'terisi'    => ['label' => 'Terisi',      'class' => 'room-badge--occupied'],
        'perbaikan' => ['label' => 'Perbaikan',   'class' => 'room-badge--repair'],
        default     => ['label' => 'Tidak Diketahui', 'class' => ''],
    };
@endphp

<article class="room-card {{ $room->status->value === 'tersedia' ? 'room-card--available' : '' }} reveal">

    {{-- Gambar --}}
    <div class="room-card__img">
        @if ($room->foto_utama)
            <img
                src="{{ asset('storage/' . $room->foto_utama) }}"
                alt="Foto Kamar {{ $room->nomor_kamar }}"
                loading="lazy"
                onerror="this.parentElement.innerHTML = this.parentElement.dataset.placeholder"
                data-placeholder='<div class=\"room-card__placeholder\"><svg width=\"48\" height=\"48\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1\"><rect x=\"3\" y=\"3\" width=\"18\" height=\"18\" rx=\"2\"/><circle cx=\"8.5\" cy=\"8.5\" r=\"1.5\"/><polyline points=\"21 15 16 10 5 21\"/></svg></div>'
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
        <div class="room-card__name">
            {{ $room->deskripsi
                ? \Illuminate\Support\Str::limit($room->deskripsi, 55)
                : 'Kamar Ensuite Standard — Berlima House' }}
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
                        @elseif (str_contains(strtolower($item), 'water') || str_contains(strtolower($item), 'heater'))
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

            @if ($room->status->value === 'tersedia')
                <a
                    href="{{ $waUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn-wa-card"
                    aria-label="Pesan kamar {{ $room->nomor_kamar }} via WhatsApp"
                >
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Pesan Sekarang
                </a>
            @else
                <span class="btn-wa-card btn-wa-card--disabled">Tidak Tersedia</span>
            @endif
        </div>
    </div>
</article>
