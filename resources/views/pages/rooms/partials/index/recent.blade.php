@if ($recentRooms->isNotEmpty())
    <section class="rooms-recent" aria-labelledby="rooms-recent-title">
        <div class="rooms-recent__inner">
            <div class="rooms-recent__head">
                <span class="section-label">Aktivitas Terbaru</span>
                <h2 id="rooms-recent-title">Kamar Terakhir Dilihat</h2>
            </div>

            <div class="rooms-recent__grid">
                @foreach ($recentRooms as $room)
                    <a href="{{ route('rooms.show', ['room' => $room->nomor_kamar]) }}" class="rooms-recent-card">
                        <div class="rooms-recent-card__image">
                            @if ($room->foto_utama)
                                <img src="{{ asset('storage/' . $room->foto_utama) }}" alt="Foto Kamar {{ $room->nomor_kamar }}" loading="lazy">
                            @else
                                <span>Kamar {{ $room->nomor_kamar }}</span>
                            @endif
                        </div>
                        <div>
                            <span>Kamar {{ $room->nomor_kamar }}</span>
                            <strong>{{ $room->tipe_kamar ?: 'Kamar Berlima Guest House' }}</strong>
                            <small>Lantai {{ $room->lantai }} · {{ (int) ($room->reviews_count ?? 0) }} ulasan</small>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
