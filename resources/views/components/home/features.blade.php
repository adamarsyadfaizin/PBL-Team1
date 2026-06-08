@props([
    'settings',
    'rooms',
    'guestProfile',
])

{{-- ─── ROOMS PREVIEW ──────────────────────────────────────── --}}
<section class="rooms" id="rooms">
  <div class="rooms-head reveal">
    <div>
      <span class="section-label">{{ $settings->rooms_label }}</span>
      <h2 class="section-title">{!! nl2br(e($settings->rooms_title)) !!}</h2>
      <p class="section-sub" style="margin-top:12px">
        {!! nl2br(e($settings->rooms_description)) !!}
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

  <div class="rooms-grid">
    @forelse ($rooms as $index => $room)
      @php
        $detailUrl = route('rooms.show', ['room' => $room->nomor_kamar]);
        $status = $room->status->value ?? $room->status;
        $statusLabel = $status === 'tersedia' ? 'Tersedia' : ucfirst((string) $status);
        $hargaHarian = 'Rp ' . number_format((float) $room->harga_harian / 1000, 0, ',', '.') . 'k';
        $averageRating = round((float) ($room->reviews_avg_rating ?? 0), 1);
        $reviewsCount = (int) ($room->reviews_count ?? 0);
      @endphp

      <a href="{{ $detailUrl }}" class="room-card {{ $index === 1 ? 'featured' : '' }} reveal home-room-card">
        <div class="room-img">
          @if ($room->foto_utama)
            <img src="{{ asset('storage/' . $room->foto_utama) }}" alt="Foto Kamar {{ $room->nomor_kamar }}" loading="lazy">
          @else
            <div class="room-img-placeholder {{ $index === 1 ? 'suite' : ($index === 2 ? 'standard' : 'deluxe') }}">
              <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1" opacity=".2">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
              </svg>
            </div>
          @endif
          <span class="room-tag {{ $status === 'tersedia' ? 'best' : '' }}">{{ $statusLabel }}</span>
        </div>
        <div class="room-body">
          <div class="room-type">Kamar {{ $room->nomor_kamar }} · Lantai {{ $room->lantai }}</div>
          <div class="room-name">{{ $room->tipe_kamar ?: 'Kamar Berlima Guest House' }}</div>
          <div class="home-room-rating" aria-label="Rating kamar {{ number_format($averageRating, 1, ',', '.') }} dari 5 berdasarkan {{ $reviewsCount }} ulasan">
            <span class="home-room-rating__stars" aria-hidden="true">
              @for ($star = 1; $star <= 5; $star++)
                <span class="{{ $star <= (int) round($averageRating) ? 'is-filled' : '' }}">&#9733;</span>
              @endfor
            </span>
            <strong>{{ $reviewsCount > 0 ? number_format($averageRating, 1, ',', '.') : '0,0' }}</strong>
            <small>{{ $reviewsCount }} ulasan</small>
          </div>
          <p class="home-room-description">
            {{ $room->deskripsi ? \Illuminate\Support\Str::limit($room->deskripsi, 92) : 'Kamar nyaman dengan fasilitas pendukung untuk istirahat harian maupun bulanan.' }}
          </p>
          @if (!empty($room->fasilitas))
            <div class="room-amenities">
              @foreach (array_slice($room->fasilitas, 0, 3) as $item)
                <div class="amenity-chip">{{ $item }}</div>
              @endforeach
            </div>
          @endif
          <div class="room-footer">
            <div class="room-price">Mulai <span style="color:#4361EE">{{ $hargaHarian }}</span> <small>/ malam</small></div>
            <span class="btn-book">Detail</span>
          </div>
        </div>
      </a>
    @empty
      <div class="home-room-empty">
        <h3>Belum ada kamar yang dipublikasikan</h3>
        <p>Aktifkan kamar dari admin agar tampil di halaman landing page.</p>
      </div>
    @endforelse

  </div>
</section>

{{-- ─── SEKILAS SUASANA ───────────────────────────────────── --}}
<section class="preview-gallery" id="preview-gallery">
  <div class="preview-head reveal">
    <div>
      <span class="section-label">{{ $settings->gallery_label }}</span>
      <h2 class="section-title">{!! nl2br(e($settings->gallery_title)) !!}</h2>
      <p class="section-sub" style="margin-top:12px">
        {!! nl2br(e($settings->gallery_description)) !!}
      </p>
    </div>
  </div>

  <div class="preview-grid">
    @foreach ($guestProfile->galleryItems(4) as $item)
      <a href="{{ route('about') }}#about-gallery" class="preview-item reveal">
        <img src="{{ !empty($item['image']) ? asset('storage/' . $item['image']) : 'https://placehold.co/600x400/E2E8F0/1E293B?text=Gallery' }}" alt="{{ $item['title'] ?? 'Gallery Berlima Guest House' }}">
        <span class="caption">{{ $item['title'] ?? 'Berlima Guest House' }}</span>
      </a>
    @endforeach

  </div>
</section>

{{-- ─── HUBUNGI KAMI ───────────────────────────────────────── --}}
<section class="facilities home-contact-section" id="home-contact">
  <a href="{{ route('contact') }}" class="home-contact-link reveal" aria-label="Buka halaman contact Berlima Guest House">
    <div class="facilities-inner home-contact-inner">
      <div class="facilities-head home-contact-head">
      <div>
        <span class="section-label">{{ $settings->facilities_label }}</span>
        <h2 class="section-title">{!! nl2br(e($settings->facilities_title)) !!}</h2>
        <p class="section-sub" style="margin-top:12px">{!! nl2br(e($settings->facilities_description)) !!}</p>
      </div>
      <span class="home-contact-button">
        Hubungi Kami
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="5" y1="12" x2="19" y2="12"/>
          <polyline points="12 5 19 12 12 19"/>
        </svg>
      </span>
      </div>
    </div>
  </a>
</section>
