<div class="auth">
<a href="{{ route('home') }}" class="auth-back">
    ← Kembali ke Beranda
  </a>
  <div class="auth-card">

    <h2 class="auth-title">Buat Akun</h2>
    <p class="auth-sub">Daftar untuk mulai menginap dengan nyaman</p>

    <form method="POST" action="{{ route('signup') }}">
      @csrf

      <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" name="name" placeholder="Masukkan nama lengkap" required>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" placeholder="contoh@email.com" required>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Minimal 8 karakter" required>
      </div>

      <div class="form-group">
        <label>No. Telepon</label>
        <input type="text" name="phone" placeholder="08xxxxxxxxxx" required>
      </div>

      <div class="form-group">
        <label>No. KTP</label>
        <input type="text" name="ktp" placeholder="Masukkan nomor KTP" required>
      </div>

      <button type="submit" class="btn-submit">Sign Up</button>
    </form>

    <p class="auth-footer">
      Sudah punya akun?
      <a>Login</a>
    </p>

  </div>
</div>