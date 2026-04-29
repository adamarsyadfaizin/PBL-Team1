<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RoomStatus: string implements HasColor, HasLabel
{
    case Tersedia  = 'tersedia';
    case Terisi    = 'terisi';
    case Perbaikan = 'perbaikan';

    /** Label Bahasa Indonesia yang ditampilkan di UI Filament. */
    public function getLabel(): string
    {
        return match ($this) {
            self::Tersedia  => 'Tersedia',
            self::Terisi    => 'Terisi',
            self::Perbaikan => 'Perbaikan',
        };
    }

    /** Warna badge yang ditampilkan di tabel Filament. */
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Tersedia  => 'success',
            self::Terisi    => 'danger',
            self::Perbaikan => 'warning',
        };
    }
}
