@extends('layouts.app')

@section('title', 'Detail Kamar ' . $room->nomor_kamar . ' - Berlima Guest House')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/rooms/shared.css') }}">
<link rel="stylesheet" href="{{ asset('css/pages/rooms/show.css') }}">
@endpush

@php
    $status = $availability->status_kamar ?? ($room->status->value ?? $room->status);
    $isActive = filled($availability?->booking_aktif_id);
    $date = fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->format('d/m/Y') : '-';
    $bookingUrl = route('rooms.booking.create', ['room' => $room->nomor_kamar]);
    $hargaHarian = 'Rp ' . number_format((float) $room->harga_harian, 0, ',', '.');
    $hargaBulanan = 'Rp ' . number_format((float) $room->harga_bulanan, 0, ',', '.');
    $deposit = 'Rp ' . number_format((float) $room->deposit, 0, ',', '.');
    $statusConfig = match ($status) {
        'tersedia' => ['label' => 'Tersedia', 'class' => 'room-status--available'],
        'terisi' => ['label' => 'Terisi', 'class' => 'room-status--occupied'],
        'perbaikan' => ['label' => 'Perbaikan', 'class' => 'room-status--repair'],
        default => ['label' => 'Tidak Diketahui', 'class' => 'room-status--muted'],
    };
@endphp

@section('content')
<section class="room-detail">
    <div class="room-detail__container">
        <a href="{{ route('rooms.index') }}" class="room-page-back">Kembali ke daftar kamar</a>

        <div class="room-detail__hero">
            @include('pages.rooms.partials.show.media')
            @include('pages.rooms.partials.show.summary')
        </div>

        @include('pages.rooms.partials.show.body')
        @include('pages.rooms.partials.show.reviews')
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/rooms/show.js') }}" defer></script>
@endpush
