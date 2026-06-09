@include('pages.rooms.partials.booking.stepper', ['complete' => true])

@php
    $bookingStatusLabels = [
        'pending' => 'Menunggu Data',
        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
        'active_stay' => 'Berhasil / Sedang Menginap',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];
    $status = $trackedBooking?->status;
    $checkIn = $trackedBooking?->tanggal_check_in?->format('d/m/Y') ?? ($bookingSuccess['check_in'] ?? '-');
    $checkOut = $trackedBooking?->tanggal_check_out?->format('d/m/Y') ?? ($bookingSuccess['check_out'] ?? '-');
    $total = (float) ($trackedBooking?->total_tagihan ?? ($bookingSuccess['total'] ?? 0));
@endphp

<div class="booking-success">
    <span class="booking-success__icon">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
            <path d="M20 6L9 17l-5-5"/>
        </svg>
    </span>
    <h2>{{ $bookingSuccess ? 'Reservasi dan bukti transfer berhasil dikirim.' : 'Status pemesanan Anda.' }}</h2>
    <p>
        @if ($status === 'active_stay')
            Reservasi Anda sudah dikonfirmasi admin. Silakan cek detail periode dan total tagihan di bawah ini.
        @elseif ($status === 'dibatalkan')
            Reservasi Anda dibatalkan. Silakan cek catatan pembatalan atau hubungi admin untuk bantuan.
        @elseif ($status === 'selesai')
            Reservasi ini sudah selesai.
        @else
            Admin akan mengecek bukti transfer dan menghubungi Anda melalui WhatsApp untuk konfirmasi reservasi.
        @endif
    </p>
    @if (! empty($bookingSuccess['date_warning']))
        <div class="booking-date-warning booking-date-warning--notice">
            <strong>Catatan tanggal reservasi</strong>
            <p>Ada reservasi lain yang sudah keluar atau diajukan pada tanggal yang sama dan masih menunggu konfirmasi. Admin akan memproses berdasarkan bukti pembayaran dan finalisasi tercepat.</p>
        </div>
    @endif
    <div class="booking-success__meta">
        <span>Kode Pemesanan</span>
        <strong>{{ $trackedBooking?->kode_booking ?? ($bookingSuccess['kode_booking'] ?? '-') }}</strong>
        <span>Status</span>
        <strong>{{ $status ? ($bookingStatusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status))) : 'Menunggu Konfirmasi' }}</strong>
        <span>Periode</span>
        <strong>{{ $checkIn }} - {{ $checkOut }}</strong>
        <span>Total dari Sistem</span>
        <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
        <span>Bukti Transfer</span>
        <strong>{{ $trackedBooking?->bukti_transfer ? 'Sudah diunggah' : 'Belum ada' }}</strong>
        @if ($trackedBooking?->tanggal_konfirmasi)
            <span>Dikonfirmasi</span>
            <strong>{{ $trackedBooking->tanggal_konfirmasi->format('d/m/Y H:i') }}</strong>
        @endif
    </div>
    @if ($trackedBooking?->alasan_pembatalan)
        <div class="booking-date-warning booking-date-warning--blocked">
            <strong>Alasan pembatalan</strong>
            <p>{{ $trackedBooking->alasan_pembatalan }}</p>
        </div>
    @endif
    <a href="{{ route('rooms.show', ['room' => $room->nomor_kamar]) }}" class="btn-wa-card">Lihat Detail Kamar</a>
</div>
