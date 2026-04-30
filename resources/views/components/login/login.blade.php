<div class="auth">

  <!-- BACK BUTTON -->
  <a href="{{ route('home') }}" class="auth-back">
    ← Kembali ke Home
  </a>

  <div class="auth-card">

    <h2 class="auth-title">Masuk Akun</h2>
    <p class="auth-sub">Silakan login untuk melanjutkan</p>

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" placeholder="contoh@email.com" required>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Masukkan password" required>
      </div>

      <button type="submit" class="btn-submit">Login</button>

      <button type="button" class="btn-test-login" onclick="fakeLogin()">
        Login Test (cuma muncul pada tahap testing)
      </button>
    </form>

    <p class="auth-footer">
      Belum punya akun?
      <a href="{{ route('signup') }}">Sign Up</a>
    </p>

  </div>

</div>