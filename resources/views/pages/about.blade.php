@extends('layouts.app')

@section('title', 'Tentang Berlima Guest House')
@section('body-class', 'page-about')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/about.css') }}">
@endpush

@section('content')
<div class="about-page">
    <div class="about-shell">
        
        <!-- HEADER / PROFILE -->
        <section class="about-section about-section--first">
            <div class="about-profile">
                <div>
                    <span class="about-kicker">Tentang Kami</span>
                    <div class="about-section__head">
                        <h1>Berlima Guest House</h1>
                        <p>Berlima Guest House adalah tempat tinggal sementara yang dirancang untuk tamu yang membutuhkan kamar nyaman, proses reservasi jelas, dan komunikasi yang mudah dengan admin. Kami melayani penyewa harian maupun bulanan dengan fokus pada ketenangan, kebersihan, dan kepastian informasi sebelum tamu datang.</p>
                    </div>
                </div>
                
                <div class="about-photo">
                    <img src="{{ asset('images/about.jpg') }}" alt="Berlima Guest House" onerror="this.src='https://images.unsplash.com/photo-1510798831971-661eb04b3739?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
                </div>
            </div>

            <div class="about-story-grid" style="margin-top: 48px;">
                <div class="about-story">
                    <h2>Pengalaman Menginap yang Praktis</h2>
                    <p>Setiap informasi kamar, harga, deposit, dan status ketersediaan ditampilkan agar calon penyewa dapat mengambil keputusan dengan lebih percaya diri sebelum mengirim reservasi.</p>
                </div>
                <div class="about-story">
                    <h2>Komunikasi Tetap Personal</h2>
                    <p>Sistem membantu menghitung dan mencatat permintaan, sementara admin tetap melakukan konfirmasi akhir melalui WhatsApp supaya tidak ada informasi yang terlewat.</p>
                </div>
            </div>
        </section>

        <!-- KOMITMEN KAMI -->
        <section class="about-section">
            <div class="about-section__head">
                <span class="about-kicker">Komitmen Kami</span>
                <h2>Hal yang kami jaga untuk setiap tamu</h2>
            </div>
            
            <div class="about-value-list">
                <div>
                    <strong>Kamar yang layak dan siap digunakan</strong>
                    <p>Kebersihan, fasilitas dasar, dan kesiapan kamar menjadi bagian utama sebelum tamu masuk.</p>
                </div>
                <div>
                    <strong>Informasi harga yang jelas</strong>
                    <p>Total tagihan mengikuti data kamar dan pilihan reservasi yang dihitung oleh sistem.</p>
                </div>
                <div>
                    <strong>Konfirmasi sebelum pembayaran</strong>
                    <p>Pembayaran dilakukan setelah admin memastikan kamar dan mengirim instruksi resmi.</p>
                </div>
            </div>
        </section>

        <!-- SEBELUM MENGIRIM RESERVASI -->
        <section class="about-section">
            <div class="about-section__head">
                <h2>Sebelum mengirim reservasi</h2>
                <p>Berlima Guest House memproses reservasi melalui pengecekan admin agar tanggal, kamar, dan pembayaran tetap jelas untuk calon penyewa.</p>
            </div>

            <div class="reservation-flow-grid">
                <div class="flow-card">
                    <span class="flow-number">01</span>
                    <h3>Reservasi belum otomatis diterima</h3>
                    <p>Data yang dikirim masuk sebagai permintaan reservasi dan akan dicek terlebih dahulu.</p>
                </div>
                <div class="flow-card">
                    <span class="flow-number">02</span>
                    <h3>Admin menghubungi melalui WhatsApp</h3>
                    <p>Admin akan mengonfirmasi ketersediaan kamar melalui nomor WhatsApp yang Anda isi.</p>
                </div>
                <div class="flow-card">
                    <span class="flow-number">03</span>
                    <h3>Pembayaran setelah konfirmasi</h3>
                    <p>Jangan melakukan pembayaran sebelum admin mengirim instruksi dan memastikan kamar.</p>
                </div>
                <div class="flow-card">
                    <span class="flow-number">04</span>
                    <h3>Total dihitung sistem</h3>
                    <p>Total tagihan mengikuti tipe sewa, tanggal, durasi, dan harga kamar yang dipilih.</p>
                </div>
            </div>
        </section>

        <!-- GALERI -->
        <section class="about-section">
            <div class="about-section__head">
                <span class="about-kicker">Galeri</span>
                <h2>Sekilas Suasana Berlima</h2>
                <p>Lihat suasana kamar, area bersama, dan tampak guest house sebelum Anda datang.</p>
            </div>
            
            <div class="about-gallery-grid">
                <figure class="about-gallery-item">
                    <img src="https://images.unsplash.com/photo-1510798831971-661eb04b3739?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Kamar Nyaman">
                    <figcaption>Kamar Nyaman</figcaption>
                </figure>
                <figure class="about-gallery-item">
                    <img src="https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Tampak Depan">
                    <figcaption>Tampak Depan</figcaption>
                </figure>
                <figure class="about-gallery-item">
                    <img src="https://images.unsplash.com/photo-1616594039964-ae9021a400a0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Ruang Santai">
                    <figcaption>Ruang Santai</figcaption>
                </figure>
                <figure class="about-gallery-item">
                    <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Area Umum">
                    <figcaption>Area Umum</figcaption>
                </figure>
            </div>
        </section>

    </div>
</div>
@endsection