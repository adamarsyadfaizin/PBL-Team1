<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    public const HOME_KEY = 'home_landing';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    public const CREATED_AT = null;

    public const UPDATED_AT = 'updated_at';

    /** @var list<string> */
    protected $fillable = [
        'key',
        'value',
        'deskripsi',
        'hero_label',
        'hero_title',
        'hero_description',
        'how_label',
        'how_title',
        'how_description',
        'how_step_1_title',
        'how_step_1_description',
        'how_step_2_title',
        'how_step_2_description',
        'how_step_3_title',
        'how_step_3_description',
        'rooms_label',
        'rooms_title',
        'rooms_description',
        'gallery_label',
        'gallery_title',
        'gallery_description',
        'facilities_label',
        'facilities_title',
        'facilities_description',
    ];

    public static function home(): self
    {
        return static::query()->whereKey(self::HOME_KEY)->first()
            ?? new static(array_merge([
                'key' => self::HOME_KEY,
                'value' => self::HOME_KEY,
                'deskripsi' => 'Pengaturan konten halaman utama.',
            ], static::defaults()));
    }

    /**
     * @return list<string>
     */
    public function highlightRoomIds(): array
    {
        $payload = json_decode((string) $this->value, true);

        if (! is_array($payload)) {
            return [];
        }

        $ids = $payload['highlight_room_ids'] ?? [];

        if (! is_array($ids)) {
            return [];
        }

        return array_values(array_unique(array_filter(
            array_map(fn (mixed $id): string => (string) $id, $ids),
            fn (string $id): bool => $id !== '',
        )));
    }

    /**
     * @param list<string> $roomIds
     */
    public function highlightRoomValue(array $roomIds): string
    {
        return json_encode([
            'type' => self::HOME_KEY,
            'highlight_room_ids' => array_values(array_unique(array_slice($roomIds, 0, 3))),
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            'hero_label' => 'Selamat Datang di Berlima',
            'hero_title' => "Temukan\nBerlima Guest\nHouse",
            'hero_description' => "Nikmati hunian nyaman dengan fasilitas lengkap dan lokasi strategis.\nTemukan tempat tinggal yang sesuai dengan kebutuhan Anda.",
            'how_label' => 'Cara Pemesanan',
            'how_title' => "Pemesanan Kamar\nSemudah 3 Langkah",
            'how_description' => 'Proses reservasi kami dirancang sederhana dan cepat, mulai dari memilih kamar hingga masuk kamar.',
            'how_step_1_title' => 'Pilih kamar',
            'how_step_1_description' => 'Buka daftar kamar, periksa foto, harga, fasilitas, penilaian, dan pilih kamar yang sesuai kebutuhan.',
            'how_step_2_title' => 'Isi data reservasi',
            'how_step_2_description' => 'Lengkapi data diri, tipe sewa, tanggal masuk, tanggal keluar, dan catatan reservasi.',
            'how_step_3_title' => 'Tunggu konfirmasi admin melalui WhatsApp',
            'how_step_3_description' => 'Admin akan mengecek ketersediaan kamar lalu menghubungi Anda melalui WhatsApp untuk konfirmasi berikutnya.',
            'rooms_label' => 'Kamar Pilihan',
            'rooms_title' => "Hunian Nyaman\nuntuk Semua Kebutuhan",
            'rooms_description' => "Pilih kamar sesuai durasi dan kebutuhan kamu — harian, mingguan, atau bulanan.\nSemua dilengkapi fasilitas modern di lingkungan yang aman dan tenang.",
            'gallery_label' => 'Sekilas Suasana',
            'gallery_title' => "Lihat Kenyamanan\ndari Dekat",
            'gallery_description' => "Intip suasana kamar dan fasilitas kami sebelum Anda datang.\nNyaman, bersih, dan siap menemani waktu istirahat Anda.",
            'facilities_label' => 'Hubungi Kami',
            'facilities_title' => "Butuh Bantuan\natau Konfirmasi Kamar?",
            'facilities_description' => 'Tim Berlima Guest House siap membantu pertanyaan kamar, ketersediaan tanggal, pembayaran, dan kebutuhan lain melalui halaman kontak.',
        ];
    }
}
