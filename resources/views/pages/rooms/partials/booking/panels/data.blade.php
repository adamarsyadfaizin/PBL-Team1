<div class="booking-panel" data-step-panel>
    <h2>Data Diri</h2>
    <p>Isi data yang bisa dihubungi admin untuk konfirmasi reservasi.</p>

    <div class="booking-grid">
        <label>
            <span>Nama lengkap</span>
            <input type="text" name="nama" value="{{ old('nama', $user?->name) }}" required maxlength="255">
        </label>
        <label>
            <span>Nomor WhatsApp</span>
            <input type="text" name="phone" value="{{ old('phone', $user?->phone) }}" required maxlength="30">
        </label>
        <label>
            <span>Email</span>
            <input type="email" name="email" value="{{ old('email', $user?->email) }}" required maxlength="255">
        </label>
        <label>
            <span>Alamat opsional</span>
            <input type="text" name="alamat" value="{{ old('alamat') }}" maxlength="500">
        </label>
    </div>
</div>
