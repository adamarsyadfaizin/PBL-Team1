@extends('layouts.auth')

@section('title', 'Edit Profil — Berlima Guest House')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/signup.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
<div class="profile">
  <a href="{{ route('profile') }}" class="auth-back">
    ← Kembali ke Profil
  </a>

  <div class="auth-card" style="max-width:480px">

    <h2 class="auth-title">Edit Profil</h2>
    <p class="auth-sub">Perbarui informasi akun Anda</p>

    {{-- ─── VALIDATION ERRORS ─── --}}
    @if ($errors->any())
      <div class="alert alert--error">
        <strong>Ada beberapa kesalahan:</strong>
        <ul class="alert__list">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}">
      @csrf
      @method('PUT')

      {{-- ─── NAMA ─── --}}
      <div class="form-group">
        <label>Nama Lengkap</label>
        <input
          type="text"
          name="name"
          value="{{ old('name', $user->name) }}"
          placeholder="Masukkan nama lengkap"
          class="{{ $errors->has('name') ? 'input--error' : '' }}"
          required
        >
        @error('name')
          <span class="field-error">{{ $message }}</span>
        @enderror
      </div>

      {{-- ─── EMAIL (readonly) ─── --}}
      <div class="form-group">
        <label>Email <span class="label-hint">(tidak dapat diubah)</span></label>
        <input type="email" value="{{ $user->email }}" disabled style="opacity:.55;cursor:not-allowed">
      </div>

      {{-- ─── NO. TELEPON ─── --}}
      <div class="form-group">
        <label>No. Telepon</label>
        <input
          type="tel"
          name="phone"
          value="{{ old('phone', $user->phone) }}"
          placeholder="08xxxxxxxxxx"
          class="{{ $errors->has('phone') ? 'input--error' : '' }}"
          required
        >
        @error('phone')
          <span class="field-error">{{ $message }}</span>
        @enderror
      </div>

      {{-- ─── NO. KTP ─── --}}
      <div class="form-group">
        <label>No. KTP <span class="label-hint">(16 digit)</span></label>
        <input
          type="text"
          name="ktp"
          value="{{ old('ktp', $user->ktp) }}"
          placeholder="1234567890123456"
          maxlength="16"
          class="{{ $errors->has('ktp') ? 'input--error' : '' }}"
          required
        >
        @error('ktp')
          <span class="field-error">{{ $message }}</span>
        @enderror
      </div>

      {{-- ─── DIVIDER ─── --}}
      <div class="form-divider">
        <span>Ganti Password (opsional)</span>
      </div>
      <p class="auth-sub" style="margin-bottom:16px;font-size:.8rem">Biarkan kosong jika tidak ingin mengganti password.</p>

      {{-- ─── PASSWORD BARU ─── --}}
      <div class="form-group">
        <label>Password Baru</label>
        <input
          type="password"
          name="password"
          id="newPassword"
          placeholder="Minimal 8 karakter (huruf & angka)"
          class="{{ $errors->has('password') ? 'input--error' : '' }}"
        >
        @error('password')
          <span class="field-error">{{ $message }}</span>
        @enderror
      </div>

      {{-- ─── KONFIRMASI PASSWORD ─── --}}
      <div class="form-group">
        <label>Konfirmasi Password Baru</label>
        <input
          type="password"
          name="password_confirmation"
          id="confirmPassword"
          placeholder="Ulangi password baru"
        >
      </div>

      {{-- ─── ACTIONS ─── --}}
      <div class="profile-actions" style="margin-top:20px">
        <button type="submit" class="btn-submit btn-edit btn-action" style="flex:2">
          Simpan Perubahan
        </button>
        <a href="{{ route('profile') }}" class="btn-submit btn-logout btn-action" style="flex:1;text-decoration:none">
          Batal
        </a>
      </div>

    </form>

  </div>
</div>
@endsection

@push('scripts')
@endpush
