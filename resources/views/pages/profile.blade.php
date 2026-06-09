@extends('layouts.auth')

@section('title', 'Profil')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
@php
  $user = auth()->user();
  $initial = strtoupper(substr((string) ($user?->name ?? 'U'), 0, 1));

  $bookingStatusLabels = [
    'pending' => 'Menunggu Data',
    'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
    'active_stay' => 'Berhasil / Sedang Menginap',
    'selesai' => 'Selesai',
    'dibatalkan' => 'Dibatalkan',
  ];
@endphp

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
        {{ $initial }}
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
        <input type="text" value="{{ $user?->name ?? '-' }}" disabled>
      </div>

      <div class="form-group">
        <label>Post-el</label>
        <input type="text" value="{{ $user?->email ?? '-' }}" disabled>
      </div>

      <div class="form-group">
        <label>No. Telepon</label>
        <input type="text" value="{{ $user?->phone ?? '-' }}" disabled>
      </div>

      <div class="form-group">
        <label>No. KTP</label>
        <input type="text" value="{{ $user?->ktp ?? '-' }}" disabled>
      </div>

    </div>

  </div>

  <div class="profile-card profile-card--wide">
    <h2 class="profile-title">Riwayat Pemesanan</h2>
    <p class="profile-sub">Daftar pemesanan yang pernah Anda kirim melalui akun ini.</p>

    @if ($bookings->isEmpty())
      <div class="booking-history-empty">
        <h3>Belum ada pemesanan</h3>
        <p>Pemesanan kamar yang Anda kirim akan muncul di sini setelah data tersimpan.</p>
        <a href="{{ route('rooms.index') }}">Lihat Kamar</a>
      </div>
    @else
      <div class="booking-history-list">
        @foreach ($bookings as $booking)
          @php
            $status = (string) $booking->status;
            $room = $booking->room;
          @endphp

          <article class="booking-history-item">
            <div class="booking-history-item__head">
              <div>
                <span>{{ $booking->kode_booking }}</span>
                <h3>{{ $room ? 'Kamar ' . $room->nomor_kamar : 'Kamar tidak tersedia' }}</h3>
              </div>
              <strong class="booking-status booking-status--{{ str_replace('_', '-', $status) }}">
                {{ $bookingStatusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}
              </strong>
            </div>

            <dl class="booking-history-meta">
              <div>
                <dt>Tanggal Masuk</dt>
                <dd>{{ $booking->tanggal_check_in?->format('d/m/Y') ?? '-' }}</dd>
              </div>
              <div>
                <dt>Tanggal Keluar</dt>
                <dd>{{ $booking->tanggal_check_out?->format('d/m/Y') ?? '-' }}</dd>
              </div>
              <div>
                <dt>Tipe Sewa</dt>
                <dd>{{ ucfirst((string) $booking->tipe_sewa) }}</dd>
              </div>
              <div>
                <dt>Total</dt>
                <dd>Rp {{ number_format((float) $booking->total_tagihan, 0, ',', '.') }}</dd>
              </div>
            </dl>

            @if ($booking->catatan_penyewa)
              <p class="booking-history-note">{{ $booking->catatan_penyewa }}</p>
            @endif
          </article>
        @endforeach
      </div>
    @endif
  </div>

</div>
@endsection
