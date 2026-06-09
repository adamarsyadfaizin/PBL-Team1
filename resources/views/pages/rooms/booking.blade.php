@extends('layouts.app')

@section('title', 'Pemesanan Kamar ' . $room->nomor_kamar . ' - Berlima Guest House')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/rooms/shared.css') }}">
<link rel="stylesheet" href="{{ asset('css/pages/rooms/booking.css') }}">
@endpush

@php
    $user = auth()->user();
    $today = now()->toDateString();
    $tomorrow = now()->addDay()->toDateString();
    $steps = $needsDataStep ? ['Data Diri', 'Reservasi', 'Pembayaran'] : ['Reservasi', 'Pembayaran'];
    $initialStep = (int) session('booking_step', 1);
    $date = fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->format('d/m/Y') : '-';
    $hargaHarian = 'Rp ' . number_format((float) $room->harga_harian, 0, ',', '.');
    $hargaBulanan = 'Rp ' . number_format((float) $room->harga_bulanan, 0, ',', '.');
    $deposit = 'Rp ' . number_format((float) $room->deposit, 0, ',', '.');
    $initialTotal = (float) $room->harga_harian + (float) $room->deposit;
    $initialTotalDisplay = 'Rp ' . number_format($initialTotal, 0, ',', '.');
    $bankName = 'Bank BCA';
    $bankAccountNumber = '1234567890';
    $bankAccountName = 'Berlima Guest House';
@endphp

@section('content')
<section class="booking-page">
    <div class="booking-page__container">
        <a href="{{ route('rooms.show', ['room' => $room->nomor_kamar]) }}" class="room-page-back">Kembali ke detail kamar</a>

        <div class="booking-layout">
            @include('pages.rooms.partials.booking.summary')

            <div class="booking-card">
                @if ($bookingSuccess || $trackedBooking)
                    @include('pages.rooms.partials.booking.success')
                @else
                    @include('pages.rooms.partials.booking.form')
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/rooms/booking.js') }}" defer></script>
@endpush
