<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class GuestProfile extends Model
{
    use HasUuids;

    protected $table = 'guest_profile';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    /** @var list<string> */
    protected $fillable = [
        'name',
        'eyebrow',
        'description',
        'main_photo',
        'stories',
        'commitment_label',
        'commitment_title',
        'commitments',
        'important_label',
        'important_title',
        'important_description',
        'important_items',
        'gallery_label',
        'gallery_title',
        'gallery_description',
        'gallery_items',
        'contact_label',
        'contact_title',
        'contact_description',
        'contact_button_label',
        'contact_items',
        'platform_title',
        'platform_description',
        'platform_links',
        'contact_faq_label',
        'contact_faq_title',
        'contact_faq_description',
        'contact_faqs',
        'feedback_title',
        'feedback_yes_label',
        'feedback_no_label',
        'feedback_prompt',
        'feedback_help_title',
        'feedback_wa_label',
        'location_label',
        'location_title',
        'location_description',
        'location_embed_url',
        'location_name',
        'location_address',
        'location_google_maps_url',
        'location_waze_url',
        'location_notes',
        'masukkan',
    ];

    protected function casts(): array
    {
        return [
            'stories' => 'array',
            'commitments' => 'array',
            'important_items' => 'array',
            'gallery_items' => 'array',
            'contact_items' => 'array',
            'platform_links' => 'array',
            'contact_faqs' => 'array',
            'location_notes' => 'array',
            'masukkan' => 'array',
        ];
    }

    public static function active(): self
    {
        return static::query()->first() ?? new static(static::defaults());
    }

    public function galleryItems(int $limit = 0): array
    {
        $items = array_values(array_filter(
            $this->gallery_items ?: [],
            fn (array $item): bool => filled($item['image'] ?? null),
        ));

        return $limit > 0 ? array_slice($items, 0, $limit) : $items;
    }

    public static function imageUrl(?string $path): string
    {
        if (! $path) {
            return asset('images/gallery/exterior.png');
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return asset(ltrim($path, '/'));
        }

        return asset('storage/' . $path);
    }

    public function whatsappUrl(string $message = 'Halo Berlima Guest House, saya ingin bertanya.'): string
    {
        $baseUrl = $this->whatsappBaseUrl();

        return $baseUrl . '?text=' . urlencode($message);
    }

    public function whatsappBaseUrl(): string
    {
        $platformUrl = collect($this->platform_links ?: [])
            ->first(fn (array $item): bool => strcasecmp((string) ($item['label'] ?? ''), 'WhatsApp') === 0)['url'] ?? null;

        if (is_string($platformUrl) && filled($platformUrl)) {
            return strtok($platformUrl, '?') ?: $platformUrl;
        }

        $contactNumber = collect($this->contact_items ?: [])
            ->first(fn (array $item): bool => str_contains(strtolower((string) ($item['label'] ?? '')), 'whatsapp'))['value'] ?? null;

        $phone = preg_replace('/\D+/', '', (string) $contactNumber);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return 'https://wa.me/' . ($phone ?: '6285707323326');
    }

    public static function defaults(): array
    {
        return [
            'name' => 'Berlima Guest House',
            'eyebrow' => 'Tentang Kami',
            'description' => 'Berlima Guest House adalah tempat tinggal sementara yang dirancang untuk tamu yang membutuhkan kamar nyaman, proses reservasi jelas, dan komunikasi yang mudah dengan admin. Kami melayani penyewa harian maupun bulanan dengan fokus pada ketenangan, kebersihan, dan kepastian informasi sebelum tamu datang.',
            'main_photo' => '/images/gallery/exterior.png',
            'stories' => [
                [
                    'title' => 'Pengalaman Menginap yang Praktis',
                    'description' => 'Setiap informasi kamar, harga, deposit, dan status ketersediaan ditampilkan agar calon penyewa dapat mengambil keputusan dengan lebih percaya diri sebelum mengirim reservasi.',
                ],
                [
                    'title' => 'Komunikasi Tetap Personal',
                    'description' => 'Sistem membantu menghitung dan mencatat permintaan, sementara admin tetap melakukan konfirmasi akhir melalui WhatsApp supaya tidak ada informasi yang terlewat.',
                ],
            ],
            'commitment_label' => 'Komitmen Kami',
            'commitment_title' => 'Hal yang kami jaga untuk setiap tamu',
            'commitments' => [
                [
                    'title' => 'Kamar yang layak dan siap digunakan',
                    'description' => 'Kebersihan, fasilitas dasar, dan kesiapan kamar menjadi bagian utama sebelum tamu masuk.',
                ],
                [
                    'title' => 'Informasi harga yang jelas',
                    'description' => 'Total tagihan mengikuti data kamar dan pilihan reservasi yang dihitung oleh sistem.',
                ],
                [
                    'title' => 'Konfirmasi sebelum pembayaran',
                    'description' => 'Pembayaran dilakukan setelah admin memastikan kamar dan mengirim instruksi resmi.',
                ],
            ],
            'important_label' => 'Informasi Penting',
            'important_title' => 'Sebelum mengirim reservasi',
            'important_description' => 'Berlima Guest House memproses reservasi melalui pengecekan admin agar tanggal, kamar, dan pembayaran tetap jelas untuk calon penyewa.',
            'important_items' => [
                [
                    'title' => 'Reservasi belum otomatis diterima',
                    'description' => 'Data yang dikirim masuk sebagai permintaan reservasi dan akan dicek terlebih dahulu.',
                ],
                [
                    'title' => 'Admin menghubungi melalui WhatsApp',
                    'description' => 'Admin akan mengonfirmasi ketersediaan kamar melalui nomor WhatsApp yang Anda isi.',
                ],
                [
                    'title' => 'Pembayaran setelah konfirmasi',
                    'description' => 'Jangan melakukan pembayaran sebelum admin mengirim instruksi dan memastikan kamar.',
                ],
                [
                    'title' => 'Total dihitung sistem',
                    'description' => 'Total tagihan mengikuti tipe sewa, tanggal, durasi, dan harga kamar yang dipilih.',
                ],
            ],
            'gallery_label' => 'Galeri',
            'gallery_title' => 'Sekilas Suasana Berlima',
            'gallery_description' => 'Lihat suasana kamar, area bersama, dan tampak guest house sebelum Anda datang.',
            'gallery_items' => [
                ['title' => 'Kamar Nyaman', 'image' => '/images/gallery/bedroom.png'],
                ['title' => 'Tampak Depan', 'image' => '/images/gallery/exterior.png'],
                ['title' => 'Ruang Santai', 'image' => '/images/gallery/lounge.png'],
                ['title' => 'Area Umum', 'image' => '/images/gallery/area-umum.png'],
            ],
            'contact_label' => 'Kontak',
            'contact_title' => 'Bagaimana kami dapat membantu Anda?',
            'contact_description' => 'Ajukan pertanyaan seputar kamar, ketersediaan, pembayaran, perubahan jadwal, atau kebutuhan lain yang ingin Anda pastikan sebelum menginap.',
            'contact_button_label' => 'Ajukan Pertanyaan',
            'contact_items' => [
                ['label' => 'WhatsApp Admin', 'value' => '0857-0732-3326', 'url' => 'https://wa.me/6285707323326'],
                ['label' => 'Surel', 'value' => 'info@berlimaguesthouse.com', 'url' => 'mailto:info@berlimaguesthouse.com'],
                ['label' => 'Jam Operasional', 'value' => 'Setiap hari, 07.00 - 22.00 WIB', 'url' => null],
                ['label' => 'Masuk / Keluar', 'value' => 'Masuk 14.00 WIB, keluar 12.00 WIB', 'url' => null],
            ],
            'platform_title' => 'Platform Digital',
            'platform_description' => 'Ikuti kanal resmi kami untuk informasi kamar, lokasi, dan pembaruan layanan.',
            'platform_links' => [
                ['label' => 'Instagram', 'url' => 'https://instagram.com/berlima_guesthouse'],
                ['label' => 'TikTok', 'url' => 'https://tiktok.com/@berlima'],
                ['label' => 'Google Maps', 'url' => 'https://maps.google.com'],
                ['label' => 'WhatsApp', 'url' => 'https://wa.me/6285707323326'],
            ],
            'contact_faq_label' => 'Pertanyaan Umum',
            'contact_faq_title' => 'Pertanyaan yang sering diajukan',
            'contact_faq_description' => 'Beberapa jawaban singkat sebelum Anda menghubungi admin atau mengisi formulir.',
            'contact_faqs' => [
                [
                    'question' => 'Apakah reservasi langsung diterima otomatis?',
                    'answer' => 'Tidak. Reservasi yang Anda kirim akan masuk sebagai permintaan dan admin akan mengecek ketersediaan kamar terlebih dahulu.',
                ],
                [
                    'question' => 'Bagaimana saya tahu kamar masih tersedia?',
                    'answer' => 'Admin akan menghubungi Anda melalui WhatsApp untuk mengonfirmasi ketersediaan kamar pada tanggal yang dipilih.',
                ],
                [
                    'question' => 'Kapan saya harus melakukan pembayaran?',
                    'answer' => 'Pembayaran dilakukan setelah admin mengonfirmasi kamar dan memberikan informasi pembayaran resmi.',
                ],
                [
                    'question' => 'Apakah total tagihan dihitung manual?',
                    'answer' => 'Tidak. Total tagihan dihitung otomatis oleh sistem berdasarkan kamar, tipe sewa, tanggal masuk, tanggal keluar, dan durasi.',
                ],
                [
                    'question' => 'Apa yang terjadi setelah saya mengirim reservasi?',
                    'answer' => 'Admin akan menghubungi nomor WhatsApp yang Anda cantumkan untuk konfirmasi ketersediaan kamar dan tahap pembayaran.',
                ],
                [
                    'question' => 'Apakah bisa memesan tanpa masuk akun?',
                    'answer' => 'Bisa. Jika belum masuk akun, Anda akan diminta mengisi data diri seperti nama, nomor WhatsApp, surel, dan alamat opsional.',
                ],
                [
                    'question' => 'Bagaimana jika tanggal yang saya pilih bentrok?',
                    'answer' => 'Admin akan memberi tahu jika kamar sudah dipesan atau sedang tidak tersedia, lalu membantu menawarkan tanggal atau kamar lain bila memungkinkan.',
                ],
                [
                    'question' => 'Bisakah saya mengubah tanggal masuk atau keluar?',
                    'answer' => 'Perubahan tanggal dapat dibicarakan dengan admin melalui WhatsApp selama kamar masih tersedia dan reservasi belum diproses final.',
                ],
            ],
            'feedback_title' => 'Apakah ini membantu?',
            'feedback_yes_label' => 'Ya',
            'feedback_no_label' => 'Tidak',
            'feedback_prompt' => 'Mohon maaf, apakah Anda memiliki masukan?',
            'feedback_help_title' => 'Perlu bantuan lain?',
            'feedback_wa_label' => 'Hubungi Kami',
            'location_label' => 'Lokasi',
            'location_title' => 'Temukan Kami',
            'location_description' => 'Berlima Guest House berada di lokasi yang mudah dijangkau. Gunakan peta untuk melihat area sekitar atau membuka rute melalui aplikasi navigasi.',
            'location_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126748.50938823!2d112.60363795!3d-7.2574719!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7fbf8381ac47f%3A0x3027a76e352be40!2sSurabaya%2C%20East%20Java!5e0!3m2!1sen!2sid!4v1698000000000!5m2!1sen!2sid',
            'location_name' => 'Berlima Guest House',
            'location_address' => 'Jl. Contoh No. 5, Surabaya',
            'location_google_maps_url' => 'https://maps.google.com',
            'location_waze_url' => 'https://waze.com',
            'location_notes' => [
                'Parkir tersedia untuk tamu.',
                'Masuk lebih awal dapat dikonfirmasi lebih dulu kepada admin.',
                'Mohon simpan nomor WhatsApp admin untuk koordinasi kedatangan.',
            ],
            'masukkan' => [],
        ];
    }
}
