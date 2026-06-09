@props(['guestProfile'])

<aside class="map-side" aria-labelledby="map-title">
    <div class="map-head">
        <span class="section-label">{{ $guestProfile->location_label }}</span>
        <h2 id="map-title" class="section-title">{{ $guestProfile->location_title }}</h2>
        <p class="section-sub">{{ $guestProfile->location_description }}</p>
    </div>

    <div class="map-wrap">
        <iframe
            src="{{ $guestProfile->location_embed_url }}"
            allowfullscreen
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
        ></iframe>

        <div class="map-pin-badge">
            <strong>{{ $guestProfile->location_name }}</strong>
            <span>{{ $guestProfile->location_address }}</span>
        </div>
    </div>

    <div class="direction-links">
        <a href="{{ $guestProfile->location_google_maps_url }}" target="_blank" rel="noopener" class="direction-link">
            Buka Google Maps
        </a>
        <a href="{{ $guestProfile->location_waze_url }}" target="_blank" rel="noopener" class="direction-link">
            Buka Waze
        </a>
    </div>

    <div class="location-notes">
        <h3>Catatan Lokasi</h3>
        <ul>
            @foreach (($guestProfile->location_notes ?: []) as $note)
                <li>{{ is_array($note) ? ($note['note'] ?? '') : $note }}</li>
            @endforeach
        </ul>
    </div>
</aside>
