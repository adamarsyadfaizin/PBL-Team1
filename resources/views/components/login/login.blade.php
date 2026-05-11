<div class="auth">

  <!-- BACK BUTTON -->
  <a href="{{ route('home') }}" class="auth-back">
    ← Kembali ke Home
  </a>

  <div class="auth-card">

    <h2 class="auth-title">Masuk Akun</h2>
    <p class="auth-sub">Silakan login untuk melanjutkan</p>

    {{-- ─── FLASH SUCCESS ─── --}}
    @if (session('success'))
      <div class="alert alert--success">
        {{ session('success') }}
      </div>
    @endif

    {{-- ─── FLASH ERROR ─── --}}
    @if (session('error'))
      <div class="alert alert--error">
        {{ session('error') }}
      </div>
    @endif

    {{-- ─── VALIDATION ERRORS ─── --}}
    @if ($errors->any())
      <div class="alert alert--error">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
      @csrf

      <div class="form-group">
        <label>Email</label>
        <input
          type="email"
          name="email"
          placeholder="contoh@email.com"
          value="{{ old('email') }}"
          class="{{ $errors->has('email') ? 'input--error' : '' }}"
          required
          autofocus
        >
        @error('email')
          <span class="field-error">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label>Password</label>
        <div class="input-password-wrap">
          <input
            type="password"
            name="password"
            id="loginPassword"
            placeholder="Masukkan password"
            class="{{ $errors->has('password') ? 'input--error' : '' }}"
            required
          >
          <span class="eye-toggle" onclick="toggleLoginPassword()">👁</span>
        </div>
        @error('password')
          <span class="field-error">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group" style="display:flex;align-items:center;gap:8px;">
        <input type="checkbox" name="remember" id="remember" style="width:auto;accent-color:var(--accent)">
        <label for="remember" style="margin:0;font-size:.85rem;color:var(--muted);cursor:pointer">Ingat saya</label>
      </div>

      <button type="submit" class="btn-submit">Login</button>
    </form>

    <p class="auth-footer">
      Belum punya akun?
      <a href="{{ route('signup') }}">Sign Up</a>
    </p>

  </div>

</div>

<script>
  function toggleLoginPassword() {
    const field = document.getElementById('loginPassword');
    const btn = document.querySelector('.eye-toggle');
    if (field.type === 'password') {
      field.type = 'text';
      btn.textContent = '𓁹';
    } else {
      field.type = 'password';
      btn.textContent = '👁';
    }
  }
</script>