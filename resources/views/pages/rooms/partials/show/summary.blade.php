<aside class="room-detail__summary">
    <div class="room-detail__summary-top">
        <span class="room-status {{ $statusConfig['class'] }}">{{ $statusConfig['label'] }}</span>
        <p class="room-detail__eyebrow">Lantai {{ $room->lantai }}</p>
    </div>
    <h1>Kamar {{ $room->nomor_kamar }}</h1>
    <p class="room-detail__desc">
        {{ $room->deskripsi ? \Illuminate\Support\Str::limit($room->deskripsi, 120) : 'Kamar nyaman untuk sewa harian maupun bulanan di Berlima Guest House.' }}
    </p>

    <div class="room-detail__facts">
        <div>
            <span>Luas</span>
            <strong>{{ $room->luas_m2 ? number_format((float) $room->luas_m2, 0, ',', '.') . ' m2' : '-' }}</strong>
        </div>
        <div>
            <span>Harian</span>
            <strong>{{ $hargaHarian }}</strong>
        </div>
        <div>
            <span>Bulanan</span>
            <strong>{{ $hargaBulanan }}</strong>
        </div>
        <div>
            <span>Deposit</span>
            <strong>{{ $deposit }}</strong>
        </div>
    </div>

    @if (($room->status->value ?? $room->status) === 'perbaikan')
        <span class="btn-wa-card btn-wa-card--disabled room-detail__book-btn">Belum Bisa Dipesan</span>
    @else
        <a href="{{ $bookingUrl }}" class="btn-wa-card room-detail__book-btn">Pesan Kamar</a>
    @endif
</aside>
