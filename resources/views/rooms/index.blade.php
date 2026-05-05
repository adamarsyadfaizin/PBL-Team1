@extends('layouts.app')

@section('title', 'Daftar Kamar — Berlima Guest House')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/rooms.css') }}">
@endpush

@section('content')

{{-- ─── PAGE HERO ──────────────────────────────────────────── --}}
<section class="rooms-hero">
    <div class="rooms-hero__glow rooms-hero__glow--left" aria-hidden="true"></div>
    <div class="rooms-hero__glow rooms-hero__glow--right" aria-hidden="true"></div>
    <div class="rooms-hero__inner">
        <span class="section-label">🛏 Kamar & Hunian</span>
        <h1 class="rooms-hero__title">
            Temukan Kamar<br>
            <span class="rooms-hero__accent">Berlima House</span>
        </h1>
        <p class="rooms-hero__sub">
            Hunian nyaman dengan fasilitas lengkap untuk kebutuhan harian dan bulanan.<br>
            Semua kamar dilengkapi AC, WiFi, dan kamar mandi dalam.
        </p>

        {{-- Filter / Stats row --}}
        <div class="rooms-hero__stats">
            <div class="rooms-stat">
                <span class="rooms-stat__val">{{ $rooms->count() }}</span>
                <span class="rooms-stat__label">Kamar Tersedia</span>
            </div>
            <div class="rooms-stat-divider"></div>
            <div class="rooms-stat">
                <span class="rooms-stat__val">
                    {{ $rooms->where('status.value', 'tersedia')->count()
                        ?: $rooms->filter(fn($r) => ($r->status->value ?? $r->status) === 'tersedia')->count() }}
                </span>
                <span class="rooms-stat__label">Bisa Dipesan</span>
            </div>
            <div class="rooms-stat-divider"></div>
            <div class="rooms-stat">
                <span class="rooms-stat__val">24/7</span>
                <span class="rooms-stat__label">Layanan</span>
            </div>
        </div>
    </div>
</section>

{{-- ─── FILTER BAR ─────────────────────────────────────────── --}}
<div class="rooms-filter" id="filter-bar">
    <div class="rooms-filter__inner">
        <span class="rooms-filter__label">Filter:</span>
        <button class="filter-chip filter-chip--active" data-filter="all">Semua</button>
        <button class="filter-chip" data-filter="tersedia">Tersedia</button>
        <button class="filter-chip" data-filter="terisi">Terisi</button>
        @foreach($rooms->pluck('lantai')->unique()->sort() as $lantai)
            <button class="filter-chip" data-filter="lantai-{{ $lantai }}">Lantai {{ $lantai }}</button>
        @endforeach
    </div>
</div>

{{-- ─── ROOMS GRID ─────────────────────────────────────────── --}}
<section class="rooms-page" id="rooms-list">
    <div class="rooms-page__container">

        @if ($rooms->isEmpty())
            {{-- Empty State --}}
            <div class="rooms-empty">
                <div class="rooms-empty__icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </div>
                <h3>Belum Ada Kamar</h3>
                <p>Kamar sedang dalam persiapan. Silakan hubungi kami untuk informasi lebih lanjut.</p>
                <a
                    href="https://wa.me/6281234567890?text=Halo+Berlima+House%2C+saya+ingin+menanyakan+info+kamar."
                    class="btn-wa-hero"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Hubungi Kami
                </a>
            </div>
        @else
            <div class="rooms-page__grid" id="rooms-grid">
                @foreach ($rooms as $room)
                    <div
                        class="room-grid-item"
                        data-status="{{ $room->status->value ?? $room->status }}"
                        data-lantai="lantai-{{ $room->lantai }}"
                    >
                        <x-room-card :room="$room" />
                    </div>
                @endforeach
            </div>

            {{-- No results after filter --}}
            <div class="rooms-no-result" id="no-result" style="display:none;">
                <p>Tidak ada kamar yang sesuai filter.</p>
            </div>
        @endif

    </div>
</section>

{{-- ─── CTA BOTTOM ─────────────────────────────────────────── --}}
<section class="rooms-cta">
    <div class="rooms-cta__inner">
        <div class="rooms-cta__content">
            <h2>Masih Bingung Memilih?</h2>
            <p>Tim kami siap membantu kamu memilih kamar yang paling sesuai dengan kebutuhan dan budget.</p>
        </div>
        <div class="rooms-cta__actions">
            <a
                href="https://wa.me/6281234567890?text=Halo+Berlima+House%2C+saya+butuh+bantuan+memilih+kamar."
                target="_blank"
                rel="noopener noreferrer"
                class="btn-wa-hero"
                id="btn-cta-wa"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Konsultasi via WhatsApp
            </a>
            <a href="{{ route('contact') }}" class="btn-outline-rooms" id="btn-cta-contact">Hubungi Kami</a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ─── Filter Chips ───────────────────────────────────
    const chips   = document.querySelectorAll('.filter-chip');
    const items   = document.querySelectorAll('.room-grid-item');
    const noRes   = document.getElementById('no-result');

    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('filter-chip--active'));
            chip.classList.add('filter-chip--active');

            const filter = chip.dataset.filter;
            let visible  = 0;

            items.forEach(item => {
                const matchAll    = filter === 'all';
                const matchStatus = item.dataset.status === filter;
                const matchLantai = item.dataset.lantai === filter;

                if (matchAll || matchStatus || matchLantai) {
                    item.style.display = '';
                    visible++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (noRes) noRes.style.display = visible === 0 ? 'block' : 'none';
        });
    });

    // ─── Scroll Reveal ──────────────────────────────────
    const reveals = document.querySelectorAll('.reveal');
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.1 });
    reveals.forEach(el => io.observe(el));
}());
</script>
@endpush
