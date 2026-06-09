<div class="booking-panel" data-step-panel>
    <h2>Reservasi</h2>
    <p>Pilih tipe sewa dan tanggal. Durasi serta total tagihan dihitung otomatis oleh sistem.</p>

    <div class="booking-grid">
        <label>
            <span>Tipe sewa</span>
            <select name="tipe_sewa" id="tipe_sewa" required>
                <option value="harian" @selected(old('tipe_sewa', 'harian') === 'harian')>Harian</option>
                <option value="bulanan" @selected(old('tipe_sewa') === 'bulanan')>Bulanan</option>
            </select>
        </label>
        <label>
            <span>Tanggal masuk</span>
            <input type="date" name="tanggal_check_in" id="tanggal_check_in" min="{{ $today }}" value="{{ old('tanggal_check_in', $today) }}" required>
        </label>
        <label>
            <span>Tanggal keluar</span>
            <input type="date" name="tanggal_check_out" id="tanggal_check_out" min="{{ $tomorrow }}" value="{{ old('tanggal_check_out', $tomorrow) }}" required>
        </label>
        <label>
            <span>Durasi sistem</span>
            <input type="text" id="durasi_display" value="1 malam" readonly>
        </label>
    </div>

    <label class="booking-field-full">
        <span>Catatan penyewa</span>
        <textarea name="catatan_penyewa" rows="4" maxlength="1200" placeholder="Contoh: rencana datang malam, permintaan lantai tertentu, atau kebutuhan lain.">{{ old('catatan_penyewa') }}</textarea>
    </label>

    <div class="booking-total">
        <span>Total tagihan sistem</span>
        <strong id="total_display">{{ $initialTotalDisplay }}</strong>
        <small>Sudah termasuk deposit jika kamar memiliki deposit.</small>
    </div>
</div>
