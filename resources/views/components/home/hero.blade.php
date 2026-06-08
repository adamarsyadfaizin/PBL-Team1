@props([
    'settings',
    'stats',
    'searchRooms',
    'guestProfile',
])

@php
    $roomCount = (int) ($stats['rooms_count'] ?? 0);
    $averageRating = (float) ($stats['average_rating'] ?? 0);
    $reviewsCount = (int) ($stats['reviews_count'] ?? 0);
    $hasHeroImage = filled($guestProfile->main_photo);
    $heroImage = $hasHeroImage ? asset('storage/' . $guestProfile->main_photo) : null;
@endphp

<section class="hero" id="home">
    <div class="hero-content">
      <span class="section-label reveal">✦ {{ $settings->hero_label }}</span>
      <h1 class="hero-title">
        <span class="orange-blob"></span>
        {!! nl2br(e($settings->hero_title)) !!}
      </h1>

      <p class="hero-subtitle">
        {!! nl2br(e($settings->hero_description)) !!}
      </p>

      <div class="hero-search" data-room-search>
        <div class="search-bar">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4361EE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
          </svg>
          <input type="text" data-room-search-input placeholder="Cari nomor kamar, tipe, fasilitas..." autocomplete="off" />
          <button class="search-btn" type="button" data-room-search-button aria-label="Cari kamar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
          </button>
        </div>
        <div class="hero-search-results" data-room-search-results hidden></div>
        <script type="application/json" data-room-search-json>@json($searchRooms)</script>
      </div>

      <div class="stats">
        <div>
          <div class="stat-value">{{ number_format($roomCount, 0, ',', '.') }}</div>
          <div class="stat-label">Jumlah Kamar</div>
        </div>
        <div>
          <div class="stat-value">{{ number_format($averageRating, 1, ',', '.') }} <span class="star">&#9733;</span></div>
          <div class="stat-label">Rating</div>
        </div>
        <div>
          <div class="stat-value">{{ number_format($reviewsCount, 0, ',', '.') }}</div>
          <div class="stat-label">Ulasan</div>
        </div>
      </div>

    </div>

    <!-- Hero Image -->
    <div class="hero-image-wrap">
      <span class="hero-visual-ring hero-visual-ring--outer" aria-hidden="true"></span>
      <span class="hero-visual-ring hero-visual-ring--inner" aria-hidden="true"></span>
      <div class="hero-floating-card hero-floating-card--top" aria-hidden="true">
        <span>Rating</span>
        <strong>{{ number_format($averageRating, 1, ',', '.') }} ★</strong>
      </div>
      <div class="hero-floating-card hero-floating-card--bottom" aria-hidden="true">
        <span>Ulasan</span>
        <strong>{{ number_format($reviewsCount, 0, ',', '.') }}</strong>
      </div>
      <div class="arch-frame">
        @if ($hasHeroImage)
          <img src="{{ $heroImage }}" alt="Foto {{ $guestProfile->name ?: 'Berlima Guest House' }}">
        @else
          <div class="arch-placeholder">
            <svg width="54" height="54" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
              <rect x="3" y="3" width="18" height="18" rx="2"/>
              <circle cx="8.5" cy="8.5" r="1.5"/>
              <polyline points="21 15 16 10 5 21"/>
            </svg>
            <span>{{ $guestProfile->name ?: 'Berlima Guest House' }}</span>
          </div>
        @endif
        <span class="arch-dot tl"></span>
        <span class="arch-dot br"></span>
      </div>
    </div>

  </section>

@once
  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const searchRoot = document.querySelector('[data-room-search]');
        if (!searchRoot) return;

        const input = searchRoot.querySelector('[data-room-search-input]');
        const button = searchRoot.querySelector('[data-room-search-button]');
        const results = searchRoot.querySelector('[data-room-search-results]');
        const jsonSource = searchRoot.querySelector('[data-room-search-json]');
        const roomsIndexUrl = @json(route('rooms.index'));
        let rooms = [];
        let activeResults = [];
        let timer = null;

        try {
          rooms = JSON.parse(jsonSource?.textContent || '[]');
        } catch (error) {
          rooms = [];
        }

        const normalize = (value) => String(value || '').toLowerCase().trim();
        const escapeHtml = (value) => String(value ?? '')
          .replaceAll('&', '&amp;')
          .replaceAll('<', '&lt;')
          .replaceAll('>', '&gt;')
          .replaceAll('"', '&quot;')
          .replaceAll("'", '&#039;');

        const roomHaystack = (room) => normalize([
          room.number,
          room.type,
          room.floor,
          room.description,
          room.status,
          room.price,
          ...(room.facilities || []),
        ].join(' '));

        const renderResults = () => {
          const query = normalize(input.value);

          if (!query) {
            activeResults = [];
            results.hidden = true;
            results.innerHTML = '';
            return;
          }

          activeResults = rooms
            .filter((room) => roomHaystack(room).includes(query))
            .slice(0, 6);

          results.hidden = false;

          if (activeResults.length === 0) {
            results.innerHTML = '<div class="hero-search-empty">Kamar tidak ditemukan.</div>';
            return;
          }

          results.innerHTML = activeResults.map((room) => {
            const status = room.status === 'tersedia' ? 'Tersedia' : room.status;

            return `
            <a class="hero-search-result" href="${escapeHtml(room.url)}">
              <span>
                <strong>Kamar ${escapeHtml(room.number)} · ${escapeHtml(room.type)}</strong>
                <small>Lantai ${escapeHtml(room.floor)} · ${escapeHtml(room.price)} / malam · ${Number(room.rating || 0).toFixed(1)} rating</small>
              </span>
              <em>${escapeHtml(status)}</em>
            </a>
          `;
          }).join('');
        };

        input.addEventListener('input', () => {
          window.clearTimeout(timer);
          timer = window.setTimeout(renderResults, 180);
        });

        button.addEventListener('click', () => {
          if (activeResults.length > 0) {
            window.location.href = activeResults[0].url;
            return;
          }

          window.location.href = roomsIndexUrl;
        });

        document.addEventListener('click', (event) => {
          if (!searchRoot.contains(event.target)) {
            results.hidden = true;
          }
        });

        input.addEventListener('focus', () => {
          if (input.value.trim()) renderResults();
        });
      });
    </script>
  @endpush
@endonce
