<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\View\View;

class RoomController extends Controller
{
    /**
     * Tampilkan halaman daftar semua kamar yang dipublikasikan.
     */
    public function index(): View
    {
        $rooms = Room::query()
            ->where('is_published', true)
            ->orderBy('lantai')
            ->orderBy('nomor_kamar')
            ->get();

        return view('rooms.index', compact('rooms'));
    }
}
