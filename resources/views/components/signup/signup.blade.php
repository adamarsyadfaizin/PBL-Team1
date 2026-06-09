<div class="auth">
<a href="{{ route('home') }}" class="auth-back">
    ← Kembali ke Beranda
  </a>
  <div class="auth-card">

    <h2 class="auth-title">Buat Akun</h2>
    <p class="auth-sub">Daftar untuk mulai menginap dengan nyaman</p>

    <form method="POST" action="{{ route('signup.store') }}">
      @csrf

      <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
        @error('name')
          <span class="form-error">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label>Post-el</label>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="contoh@postel.com" required>
        @error('email')
          <span class="form-error">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label>Kata Sandi</label>
        <input type="password" name="password" placeholder="Minimal 8 karakter" required>
        @error('password')
          <span class="form-error">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label>No. Telepon</label>
        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required>
        @error('phone')
          <span class="form-error">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label>No. KTP</label>
        <input type="text" name="ktp" value="{{ old('ktp') }}" placeholder="Masukkan nomor KTP" required>
        @error('ktp')
          <span class="form-error">{{ $message }}</span>
        @enderror
      </div>

      <button type="submit" class="btn-submit">Daftar</button>
    </form>

    <p class="auth-footer">
      Sudah punya akun?
      <a href="{{ route('login') }}">Masuk</a>
    </p>

  </div>
</div>
