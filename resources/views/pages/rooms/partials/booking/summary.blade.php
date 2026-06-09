<aside class="booking-summary">
    <span class="room-status {{ ($room->status->value ?? $room->status) === 'tersedia' ? 'room-status--available' : 'room-status--occupied' }}">
        Kamar {{ ucfirst($room->status->value ?? $room->status) }}
    </span>
    <h1>Pemesanan Kamar {{ $room->nomor_kamar }}</h1>
    <p>{{ $room->deskripsi ?: 'Lengkapi reservasi, lalu admin akan menghubungi Anda untuk konfirmasi.' }}</p>

    <div class="booking-summary__prices">
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

    <div class="booking-summary__availability">
        <strong>Status keluar</strong>
        @if (filled($availability?->booking_aktif_id))
            <span>Belum keluar: {{ $date($availability->tanggal_check_in) }} - {{ $date($availability->tanggal_check_out) }}</span>
        @else
            <span>Tidak ada pemesanan aktif hari ini.</span>
        @endif
    </div>

    @if ($upcomingBookings->isNotEmpty())
        <div class="booking-summary__availability">
            <strong>Jadwal aktif</strong>
            @foreach ($upcomingBookings->take(3) as $booking)
                <span>{{ $booking->tanggal_check_in->format('d/m/Y') }} - {{ $booking->tanggal_check_out->format('d/m/Y') }}</span>
            @endforeach
        </div>
    @endif
</aside>
