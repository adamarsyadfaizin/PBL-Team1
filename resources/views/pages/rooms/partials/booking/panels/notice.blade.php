<div class="booking-panel" data-step-panel>
    <h2>Pembayaran & Bukti Transfer</h2>
    <p class="booking-notice">
        Transfer sesuai total tagihan sistem ke rekening Berlima Guest House, lalu unggah bukti transfer.
        Admin akan mengecek bukti transfer dan menghubungi Anda melalui WhatsApp untuk proses reservasi.
    </p>

    <div class="booking-date-warning" data-date-warning hidden>
        <strong data-date-warning-title>Perhatian tanggal reservasi</strong>
        <p data-date-warning-text></p>
    </div>

    <div class="booking-payment">
        <div class="booking-bank-card">
            <span>Nomor Rekening Guest House</span>
            <strong>{{ $bankAccountNumber }}</strong>
            <small>{{ $bankName }} a.n. {{ $bankAccountName }}</small>
        </div>

        <div class="booking-bank-card booking-bank-card--total">
            <span>Total tagihan sistem</span>
            <strong id="payment_total_display">{{ $initialTotalDisplay }}</strong>
            <small>Nominal akhir dihitung otomatis dari tipe sewa, durasi, dan deposit.</small>
        </div>
    </div>

    <label class="booking-upload">
        <span>Unggah bukti transfer</span>
        <input
            type="file"
            name="bukti_transfer"
            accept="image/jpeg,image/png,image/webp,application/pdf"
            required
        >
        <small>Format JPG, PNG, WebP, atau PDF. Maksimal 4 MB.</small>
    </label>

    <div class="booking-final-check">
        <span>Kamar</span>
        <strong>{{ $room->nomor_kamar }}</strong>
        <span>Rekening</span>
        <strong>{{ $bankName }} - {{ $bankAccountNumber }}</strong>
        <span>Atas nama</span>
        <strong>{{ $bankAccountName }}</strong>
    </div>
</div>
