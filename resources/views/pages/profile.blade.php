@extends('layouts.auth')

@section('title', 'Profil — Berlima Guest House')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/signup.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
<div class="profile">
  <a href="{{ route('home') }}" class="auth-back">
    ← Kembali ke Beranda
  </a>

  {{-- ─── FLASH MESSAGES ─── --}}
  @if (session('success'))
    <div class="alert alert--success" style="max-width:520px;margin:0 auto 20px;">
      {{ session('success') }}
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert--error" style="max-width:520px;margin:0 auto 20px;">
      {{ session('error') }}
    </div>
  @endif

  <div class="profile-card">

    {{-- ─── HEADER ─── --}}
    <h2 class="profile-title">Detail Akun</h2>
    <p class="profile-sub">Kelola informasi akun Anda</p>

    {{-- ─── AVATAR ─── --}}
    <div class="profile-avatar-wrap">
      <div class="profile-avatar">
        {{ strtoupper(substr($user->name, 0, 1)) }}
        <div class="profile-avatar-overlay">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="white" stroke-width="2">
            <path d="M23 19V7a2 2 0 0 0-2-2h-3l-2-2H8L6 5H3a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2z"/>
            <circle cx="12" cy="13" r="4"/>
          </svg>
        </div>
      </div>
    </div>

    {{-- ─── ROLE BADGE ─── --}}
    <div class="profile-role-badge {{ $user->role === 'admin' ? 'badge--admin' : 'badge--user' }}">
      {{ $user->role === 'admin' ? 'Administrator' : 'User' }}
    </div>

    {{-- ─── DATA ─── --}}
    <div class="profile-form">

      <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" value="{{ $user->name }}" disabled>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="text" value="{{ $user->email }}" disabled>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" value="placeholder_password" disabled>
      </div>

      <div class="form-group">
        <label>No. Telepon</label>
        <input type="text" value="{{ $user->phone ?? '—' }}" disabled>
      </div>

      <div class="form-group">
        <label>No. KTP</label>
        <input type="text" value="{{ $user->ktp ?? '—' }}" disabled>
      </div>

    </div>

    {{-- ─── ACTIONS ─── --}}
    <div class="profile-actions">
      <a href="{{ route('profile.edit') }}" class="btn-submit btn-edit btn-action">
        Edit Profil
      </a>

      <form method="POST" action="{{ route('logout') }}" style="flex:1">
        @csrf
        <button type="submit" class="btn-submit btn-logout btn-action">
          Logout
        </button>
      </form>

      <button
        type="button"
        class="btn-submit btn-delete btn-action"
        onclick="confirmDelete()"
      >
        Hapus Akun
      </button>
    </div>

    {{-- ─── DELETE CONFIRMATION MODAL ─── --}}
    <div id="deleteModal" class="modal-overlay" style="display:none">
      <div class="modal-box">
        <h3>Hapus Akun?</h3>
        <p>Tindakan ini tidak dapat dibatalkan. Semua data Anda akan dihapus permanen.</p>
        <div class="modal-actions">
          <button onclick="closeModal()" class="btn-submit btn-logout" style="flex:1">Batal</button>
          <form method="POST" action="{{ route('profile.destroy') }}" style="flex:1">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-submit btn-delete" style="width:100%">
              Ya, Hapus
            </button>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
  function confirmDelete() {
    document.getElementById('deleteModal').style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
  }
</script>
@endpush