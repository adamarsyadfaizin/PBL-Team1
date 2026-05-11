<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestRequest extends Model
{
    protected $fillable = [
        'nama',
        'phone',
        'email',

        'request_type',

        'checkin',
        'checkout',

        'tipe_kamar',
        'tipe_sewa',
        'jumlah_tamu',

        'complaint_category',

        'pesan',

        'status',
    ];
}