@props([
    'settings',
    'recommendedRoom' => null,
])

@php
    $roomDetailUrl = $recommendedRoom
        ? route('rooms.show', ['room' => $recommendedRoom->nomor_kamar])
        : route('rooms.index');
    $averageRating = $recommendedRoom ? round((float) ($recommendedRoom->reviews_avg_rating ?? 0), 1) : 0.0;
    $reviewsCount = $recommendedRoom ? (int) ($recommendedRoom->reviews_count ?? 0) : 0;
    $dailyPrice = $recommendedRoom
        ? 'Rp ' . number_format((float) $recommendedRoom->harga_harian, 0, ',', '.')
        : null;
    $roomType = $recommendedRoom?->tipe_kamar ?: 'Kamar Berlima Guest House';
    $roomMeta = collect([
        $recommendedRoom?->lantai ? 'Lantai ' . $recommendedRoom->lantai : null,
        $recommendedRoom?->luas_m2 ? number_format((float) $recommendedRoom->luas_m2, 0, ',', '.') . ' m2' : null,
    ])->filter()->implode(' · ');
    $recommendedRoomPhotoUrl = $recommendedRoom?->foto_utama_url;
@endphp

<section class="how">
    <div class="how-inner">
      <div>
        <span class="section-label reveal">✦ {{ $settings->how_label }}</span>
        <h2 class="section-title reveal">{!! nl2br(e($settings->how_title)) !!}</h2>
        <p class="section-sub reveal">{!! nl2br(e($settings->how_description)) !!}</p>

        <div class="how-steps">
          <div class="step reveal">
            <div class="step-num">01</div>
            <div class="step-body">
              <h4>{{ $settings->how_step_1_title }}</h4>
              <p>{{ $settings->how_step_1_description }}</p>
            </div>
          </div>
          <div class="step reveal">
            <div class="step-num orange">02</div>
            <div class="step-body">
              <h4>{{ $settings->how_step_2_title }}</h4>
              <p>{{ $settings->how_step_2_description }}</p>
            </div>
          </div>
          <div class="step reveal">
            <div class="step-num green">03</div>
            <div class="step-body">
              <h4>{{ $settings->how_step_3_title }}</h4>
              <p>{{ $settings->how_step_3_description }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="how-visual reveal">
        <div class="how-card-big">
          <div class="how-card-img">
            @if ($recommendedRoomPhotoUrl)
              <img
                src="{{ $recommendedRoomPhotoUrl }}"
                alt="Foto Kamar {{ $recommendedRoom->nomor_kamar }}"
                loading="lazy"
                onerror="this.hidden=true;this.nextElementSibling.hidden=false;"
              >
            @endif

            <div class="how-card-placeholder" @if ($recommendedRoomPhotoUrl) hidden @endif>
              <svg width="54" height="54" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
              </svg>
              <span>Foto kamar belum tersedia</span>
            </div>
          </div>
          <div class="how-card-body">
            @if ($recommendedRoom)
              <h4>Kamar {{ $recommendedRoom->nomor_kamar }}</h4>
              <p>{{ $roomType }}{{ $roomMeta ? ' · ' . $roomMeta : '' }}</p>

              <div class="how-room-rating">
                @if ($reviewsCount > 0)
                  <div class="stars-row" aria-hidden="true">
                    @for ($star = 1; $star <= 5; $star++)
                      <span class="{{ $star <= (int) round($averageRating) ? 'is-filled' : '' }}">&#9733;</span>
                    @endfor
                  </div>
                  <strong>{{ number_format($averageRating, 1, ',', '.') }}</strong>
                  <small>{{ $reviewsCount }} {{ $reviewsCount === 1 ? 'ulasan' : 'ulasan' }}</small>
                @else
                  <span class="review-empty">Belum ada ulasan</span>
                @endif
              </div>
            @else
              <h4>Belum ada kamar rekomendasi</h4>
              <p>Data kamar belum tersedia dari database.</p>
            @endif

            <div class="how-card-footer">
              <div class="how-price">
                {{ $dailyPrice ?? '-' }} <span>/ malam</span>
              </div>
              <a class="btn-sm" href="{{ $roomDetailUrl }}">{{ $recommendedRoom ? 'Lihat Detail' : 'Lihat Kamar' }}</a>
            </div>
          </div>
        </div>

        <div class="badge-float top-right">
          <div class="badge-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4361EE" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </div>
          <div>
            <h5>Rekomendasi</h5>
            <p>Berdasarkan ulasan aktual</p>
          </div>
        </div>

        <div class="badge-float bottom-left">
          @if ($recommendedRoom && $reviewsCount > 0)
            <div class="stars-row" aria-hidden="true">
              @for ($star = 1; $star <= 5; $star++)
                <span class="{{ $star <= (int) round($averageRating) ? 'is-filled' : '' }}">&#9733;</span>
              @endfor
            </div>
            <h5>{{ number_format($averageRating, 1, ',', '.') }} penilaian rata-rata</h5>
            <p>{{ $reviewsCount }} ulasan aktual</p>
          @else
            <h5>Belum ada ulasan</h5>
            <p>Penilaian akan muncul setelah pengguna memberi ulasan.</p>
          @endif
        </div>
      </div>
    </div>
  </section>
