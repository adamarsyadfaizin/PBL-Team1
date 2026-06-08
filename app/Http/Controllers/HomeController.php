<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $homeSettings = (object) [
            'hero_label' => 'Selamat Datang',
            'hero_title' => 'Berlima Guest House',
            'hero_description' => 'Kenyamanan tempat tinggal dengan fasilitas lengkap dan lokasi strategis.',
            'how_label' => 'Cara Memesan',
            'how_title' => 'Pesan Kamar Mudah',
            'how_description' => 'Ikuti langkah mudah ini untuk memesan kamar idaman Anda.',
            'how_step_1_title' => 'Pilih Kamar',
            'how_step_1_description' => 'Cari kamar yang Anda suka di daftar.',
            'how_step_2_title' => 'Pesan',
            'how_step_2_description' => 'Lakukan pemesanan dan konfirmasi.',
            'how_step_3_title' => 'Nikmati',
            'how_step_3_description' => 'Nikmati kenyamanan masa inap Anda.',
            'rooms_label' => 'Kamar Kami',
            'rooms_title' => 'Pilihan Kamar',
            'rooms_description' => 'Temukan kamar terbaik yang sesuai dengan kebutuhan dan budget Anda.',
            'gallery_label' => 'Galeri',
            'gallery_title' => 'Suasana Guest House',
            'gallery_description' => 'Lihat fasilitas dan kenyamanan yang kami tawarkan.',
            'facilities_label' => 'Hubungi Kami',
            'facilities_title' => 'Butuh Bantuan?',
            'facilities_description' => 'Hubungi kami untuk informasi lebih lanjut mengenai pemesanan.',
        ];

        $guestProfile = new class {
            public $name = 'Berlima Guest House';
            public $main_photo = null;
            public function galleryItems($count = 4) {
                return [
                    ['image' => null, 'title' => 'Tampak Depan'],
                    ['image' => null, 'title' => 'Ruang Bersantai'],
                    ['image' => null, 'title' => 'Kamar Tidur'],
                    ['image' => null, 'title' => 'Kamar Mandi'],
                ];
            }
        };

        $homeStats = [
            'rooms_count' => Room::query()->count(),
            'average_rating' => 4.8,
            'reviews_count' => 24,
        ];

        $highlightRooms = Room::query()
            ->where('is_published', true)
            ->orderBy('nomor_kamar')
            ->limit(3)
            ->get()
            ->map(function ($room) {
                $room->reviews_avg_rating = 4.8;
                $room->reviews_count = 12;
                return $room;
            });

        $searchRooms = Room::query()
            ->where('is_published', true)
            ->orderBy('nomor_kamar')
            ->get()
            ->map(fn (Room $room): array => [
                'number' => (string) $room->nomor_kamar,
                'type' => $room->tipe_kamar ?: 'Kamar Berlima Guest House',
                'floor' => (string) $room->lantai,
                'description' => Str::limit((string) $room->deskripsi, 90),
                'facilities' => $room->fasilitas,
                'price' => 'Rp ' . number_format((float) $room->harga_harian, 0, ',', '.'),
                'status' => (string) ($room->status->value ?? $room->status),
                'rating' => 4.8,
                'reviews' => 12,
                'url' => route('rooms.show', ['room' => $room->nomor_kamar]),
            ])
            ->values();

        return view('pages.home', compact('homeSettings', 'highlightRooms', 'guestProfile', 'homeStats', 'searchRooms'));
    }
}
