<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'is_published',
    ];

    /**
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'fasilitas'     => 'array',
            'harga_harian'  => 'decimal:2',
            'harga_bulanan' => 'decimal:2',
            'deposit'       => 'decimal:2',
            'luas_m2'       => 'decimal:2',
            'is_published'  => 'boolean',
            'status'        => RoomStatus::class,
        ];
    }
}
