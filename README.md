<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Dokumentasi Perubahan - 11 Mei 2026

Catatan ini merangkum perubahan yang ditambahkan pada hari ini, terutama bagian database dan alur form contact/request tamu.

### Database

Bagian database yang ditambahkan atau diubah hari ini:

| File | Perubahan | Tujuan |
| --- | --- | --- |
| `database/migrations/2026_05_11_075633_create_guest_requests_table.php` | Membuat tabel baru `guest_requests` | Menyimpan request dari halaman contact, seperti cek ketersediaan, booking, perubahan jadwal, pembatalan, keluhan, dan pertanyaan umum. |
| `database/migrations/2026_05_11_083950_add_tipe_kamar_to_rooms_table.php` | Menambahkan kolom `tipe_kamar` ke tabel `rooms` | Agar pilihan tipe kamar dari data kamar bisa dipakai di form contact/booking. |

Struktur utama tabel `guest_requests`:

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | bigint | Primary key. |
| `nama` | string | Nama lengkap pemesan, wajib diisi. |
| `phone` | string | Nomor WhatsApp/HP, wajib diisi. |
| `email` | string nullable | Email pemesan, opsional. |
| `request_type` | string | Jenis permintaan, misalnya `availability`, `booking`, `reschedule`, `cancel`, `complaint`, atau `general`. |
| `checkin` | date nullable | Tanggal check-in untuk request booking/ketersediaan. |
| `checkout` | date nullable | Tanggal check-out untuk request booking/ketersediaan. |
| `tipe_kamar` | string nullable | Tipe kamar yang dipilih. |
| `tipe_sewa` | string nullable | Jenis sewa, misalnya harian atau bulanan. |
| `jumlah_tamu` | string nullable | Jumlah tamu. |
| `budget` | decimal nullable | Budget request jika nanti dipakai. |
| `kode_booking` | string nullable | Kode booking jika nanti dipakai untuk pengelolaan booking. |
| `metode_pembayaran` | string nullable | Metode pembayaran jika nanti dipakai. |
| `nama_pengirim` | string nullable | Nama pengirim pembayaran jika nanti dipakai. |
| `complaint_category` | string nullable | Kategori keluhan. |
| `pesan` | text nullable | Pesan atau catatan tambahan dari tamu. |
| `status` | string | Status request, default `pending`. |
| `created_at`, `updated_at` | timestamp | Timestamp bawaan Laravel. |

Catatan penting database:

- Tabel `guest_requests` sudah disiapkan untuk kebutuhan form contact yang dinamis.
- Model `GuestRequest` saat ini hanya membuka mass assignment untuk field yang sudah dipakai oleh form dan controller.
- Kolom `tipe_kamar` di tabel `rooms` dipakai untuk membuat opsi tipe kamar pada form contact.

### Model

Model yang ditambahkan hari ini:

- `app/Models/GuestRequest.php`
  - Dipakai untuk menyimpan data dari form contact ke tabel `guest_requests`.
  - Field yang dapat diisi mass assignment: `nama`, `phone`, `email`, `request_type`, `checkin`, `checkout`, `tipe_kamar`, `tipe_sewa`, `jumlah_tamu`, `complaint_category`, `pesan`, dan `status`.

- `app/Models/Contact.php`
  - Model placeholder untuk kebutuhan contact jika nanti ingin dipisah dari `GuestRequest`.

### Controller dan Route

Controller yang ditambahkan:

- `app/Http/Controllers/ContactController.php`
  - Method `index()` mengambil data `Room::all()` lalu mengirimkannya ke view `pages.contact`.
  - Method `store()` melakukan validasi request contact dan menyimpan data ke tabel `guest_requests`.
  - Setelah data berhasil disimpan, user diarahkan kembali dengan pesan sukses.

Route yang ditambahkan/diubah di `routes/web.php`:

- `GET /contact` menggunakan `ContactController@index`.
- `POST /contact` menggunakan `ContactController@store` dengan nama route `contact.store`.
- Route home diperbaiki agar memakai view `pages.home`.

### View Contact dan Form

Perubahan pada halaman contact:

- `resources/views/pages/contact.blade.php`
  - Komponen form sekarang menerima data kamar dengan `<x-contact.form :rooms="$rooms" />`.

- `resources/views/components/contact/form.blade.php`
  - Form contact sekarang memakai method `POST` ke route `contact.store`.
  - Ditambahkan `@csrf`.
  - Field form disesuaikan dengan struktur `guest_requests`.
  - Ditambahkan pilihan `request_type` untuk menentukan jenis permintaan.
  - Section dinamis ditambahkan untuk:
    - Booking / cek ketersediaan.
    - Reschedule / cancel booking.
    - Complaint / bantuan.
  - Opsi `tipe_kamar` diambil dari data `rooms` berdasarkan kolom `tipe_kamar`.
  - Setelah submit berhasil, form menampilkan pesan sukses dari session.

### Fix Layout Contact

Fix tampilan yang dikerjakan:

- `resources/views/components/contact/map.blade.php`
  - Menghapus satu closing `</div>` berlebih yang membuat komponen map keluar dari grid form-map.
  - Map kembali berada di kolom kanan pada layout desktop.

- `public/css/contact.css`
  - Menambahkan style untuk class baru dari form: `form-section-title`, `dynamic-section`, dan wrapper `#contact-form-wrap`.
  - Memperbaiki layout grid form dan map dengan `minmax(0, 1fr)` dan `min-width: 0` agar form tidak mendorong lebar map.
  - Menjaga `.map-side`, `.map-wrap`, `.direction-link`, dan badge map supaya tetap berada dalam kolomnya.
  - Menambahkan responsive fix untuk layar kecil, termasuk form satu kolom, map tinggi stabil, tombol hero contact, dan quick info card agar tidak membuat overflow horizontal.

### Verifikasi

Verifikasi yang sudah dilakukan:

- `php artisan test` dijalankan menggunakan PHP Laragon:

```bash
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test
```

Hasil:

- 2 test passed.
- Halaman `http://127.0.0.1:8000/contact` berhasil diakses dengan status `200 OK`.
- Screenshot desktop dan mobile digunakan untuk mengecek posisi form dan map.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
