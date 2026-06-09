<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'bookings';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'kode_booking',
        'user_id',
        'room_id',
        'tipe_sewa',
        'tanggal_check_in',
        'tanggal_check_out',
        'durasi',
        'harga_snapshot',
        'total_tagihan',
        'catatan_penyewa',
        'bukti_transfer',
        'status',
        'alasan_pembatalan',
        'dikonfirmasi_oleh',
        'tanggal_konfirmasi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_check_in'  => 'date',
            'tanggal_check_out' => 'date',
            'tanggal_konfirmasi' => 'datetime',
            'harga_snapshot'    => 'decimal:2',
            'total_tagihan'     => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }
}
