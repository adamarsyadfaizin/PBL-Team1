<div class="auth">
  <a href="{{ route('home') }}" class="auth-back">
    ← Kembali ke Beranda
  </a>

  <div class="auth-card">

    <h2 class="auth-title">Buat Akun</h2>
    <p class="auth-sub">Daftar untuk mulai menginap dengan nyaman</p>

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

    {{-- ─── VALIDATION ERRORS SUMMARY ─── --}}
    @if ($errors->any())
      <div class="alert alert--error">
        <strong>Oops! Ada beberapa kesalahan:</strong>
        <ul class="alert__list">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('signup.store') }}">
      @csrf

      <div class="form-group">
        <label>Nama Lengkap</label>
        <input
          type="text"
          name="name"
          placeholder="Masukkan nama lengkap"
          value="{{ old('name') }}"
          class="{{ $errors->has('name') ? 'input--error' : '' }}"
          required
        >
        @error('name')
          <span class="field-error">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label>Email</label>
        <input
          type="email"
          name="email"
          placeholder="contoh@email.com"
          value="{{ old('email') }}"
          class="{{ $errors->has('email') ? 'input--error' : '' }}"
          required
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
            id="signupPassword"
            placeholder="Minimal 8 karakter (huruf & angka)"
            class="{{ $errors->has('password') ? 'input--error' : '' }}"
            required
          >
          <span class="eye-toggle" onclick="toggleSignupPassword('signupPassword', this)">👁</span>
        </div>
        @error('password')
          <span class="field-error">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label>Konfirmasi Password</label>
        <div class="input-password-wrap">
          <input
            type="password"
            name="password_confirmation"
            id="signupPasswordConfirm"
            placeholder="Ulangi password"
            required
          >
          <span class="eye-toggle" onclick="toggleSignupPassword('signupPasswordConfirm', this)">👁</span>
        </div>
      </div>

      <div class="form-group">
        <label>No. Telepon</label>
        <input
          type="tel"
          name="phone"
          placeholder="08xxxxxxxxxx"
          value="{{ old('phone') }}"
          class="{{ $errors->has('phone') ? 'input--error' : '' }}"
          required
        >
        @error('phone')
          <span class="field-error">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label>No. KTP <span class="label-hint">(16 digit)</span></label>
        <input
          type="text"
          name="ktp"
          placeholder="1234567890123456"
          value="{{ old('ktp') }}"
          maxlength="16"
          class="{{ $errors->has('ktp') ? 'input--error' : '' }}"
          required
        >
        @error('ktp')
          <span class="field-error">{{ $message }}</span>
        @enderror
      </div>

      <button type="submit" class="btn-submit">Buat Akun</button>
    </form>

    <p class="auth-footer">
      Sudah punya akun?
      <a href="{{ route('login') }}">Login</a>
    </p>

  </div>
</div>

<script>
  function toggleSignupPassword(fieldId, btn) {
    const field = document.getElementById(fieldId);
    if (field.type === 'password') {
      field.type = 'text';
      btn.textContent = '𓁹';
    } else {
      field.type = 'password';
      btn.textContent = '👁';
    }
  }
</script>