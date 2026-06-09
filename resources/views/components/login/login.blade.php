<div class="auth">

  <!-- BACK BUTTON -->
  <a href="{{ route('home') }}" class="auth-back">
    ← Kembali ke Beranda
  </a>

  <div class="auth-card">

    <h2 class="auth-title">Masuk Akun</h2>
    <p class="auth-sub">Silakan masuk untuk melanjutkan</p>

    <form method="POST" action="{{ route('login.store') }}">
      @csrf

      <div class="form-group">
        <label>Post-el</label>
        <input type="email" name="email" placeholder="contoh@postel.com" required>
      </div>

      <div class="form-group">
        <label>Kata Sandi</label>
        <input type="password" name="password" placeholder="Masukkan kata sandi" required>
      </div>

      <button type="submit" class="btn-submit">Masuk</button>
    </form>

    <p class="auth-footer">
      Belum punya akun?
      <a href="{{ route('signup') }}">Daftar</a>
    </p>

  </div>

</div>
