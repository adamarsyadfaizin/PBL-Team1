<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory, HasUuids;

    /** @var string */
    protected $table = 'rooms';

    /** @var string */
    protected $primaryKey = 'id';

    /** @var bool UUID bukan integer auto-increment */
    public $incrementing = false;

    /** @var string */
    protected $keyType = 'string';

    /** @var list<string> */
    protected $fillable = [
        'nomor_kamar',
        'lantai',
        'luas_m2',
        'deskripsi',
        'fasilitas',
        'harga_harian',
        'harga_bulanan',
        'deposit',
        'status',
        'foto_utama',
        'gallery_images',
        'video_path',
        'tipe_kamar',
        'is_published',
    ];

    /**
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'harga_harian' => 'decimal:2',
            'harga_bulanan' => 'decimal:2',
            'deposit' => 'decimal:2',
            'luas_m2' => 'decimal:2',
            'is_published' => 'boolean',
            'status' => RoomStatus::class,
            'gallery_images' => 'array',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(RoomReview::class);
    }

    public function getFotoUtamaUrlAttribute(): ?string
    {
        return $this->publicMediaUrl($this->foto_utama);
    }

    /**
     * @return list<string>
     */
    public function getGalleryImageUrlsAttribute(): array
    {
        return collect($this->gallery_images ?? [])
            ->filter(fn (mixed $path): bool => is_string($path) && trim($path) !== '')
            ->map(fn (string $path): ?string => $this->publicMediaUrl($path))
            ->filter()
            ->values()
            ->all();
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->publicMediaUrl($this->video_path);
    }

    public function publicMediaUrl(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        return '/storage/'.$path;
    }

    /**
     * Database production menyimpan fasilitas sebagai PostgreSQL text[].
     */
    public function getFasilitasAttribute(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null || $value === '') {
            return [];
        }

        $trimmed = trim((string) $value, '{}');

        if ($trimmed === '') {
            return [];
        }

        return array_values(array_filter(
            str_getcsv($trimmed, ',', '"', '\\'),
            fn (string $item): bool => trim($item) !== ''
        ));
    }

    public function setFasilitasAttribute(mixed $value): void
    {
        if ($value === null || $value === '' || $value === []) {
            $this->attributes['fasilitas'] = null;

            return;
        }

        $items = is_array($value) ? $value : [$value];
        $items = array_values(array_filter(array_map(
            fn (mixed $item): string => trim((string) $item),
            $items
        )));

        $this->attributes['fasilitas'] = '{'.implode(',', array_map(
            fn (string $item): string => '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], $item).'"',
            $items
        )).'}';
    }
}
