@extends('layouts.auth')

@section('title', 'Profile')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
<div class="profile">
  <a href="{{ route('home') }}" class="auth-back">
    ← Kembali ke Beranda
  </a>

  <div class="profile-card">

    <!-- HEADER -->
    <h2 class="profile-title">Detail Akun</h2>
    <p class="profile-sub">Kelola informasi akun Anda</p>

    <!-- AVATAR -->
    <div class="profile-avatar-wrap">
      <div class="profile-avatar">
        A
        <div class="profile-avatar-overlay">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="white" stroke-width="2">
            <path d="M23 19V7a2 2 0 0 0-2-2h-3l-2-2H8L6 5H3a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2z"/>
            <circle cx="12" cy="13" r="4"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- FORM -->
    <div class="profile-form">

      <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" value="Adam" disabled>
      </div>

      <div class="form-group">
        <label>Password</label>
        <div class="input-password">
          <input type="password" id="passwordField" value="12345678" disabled>
          <span onclick="togglePassword()" class="eye">
            👁
          </span>
        </div>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="text" value="adam@email.com" disabled>
      </div>

      <div class="form-group">
        <label>No. Telepon</label>
        <input type="text" value="08123456789" disabled>
      </div>

      <div class="form-group">
        <label>No. KTP</label>
        <input type="text" value="1234567890123456" disabled>
      </div>

    </div>

  </div>

</div>
@endsection