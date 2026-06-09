<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\GuestProfile;
use App\Models\RoomReview;
use App\Models\SystemSetting;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $homeSettings = SystemSetting::home();
        $guestProfile = GuestProfile::active();
        $reviewStatsQuery = RoomReview::query()
            ->whereNotNull('comment')
            ->where('comment', '<>', '');

        $homeStats = [
            'rooms_count' => Room::query()->count(),
            'average_rating' => round((float) ((clone $reviewStatsQuery)->avg('rating') ?? 0), 1),
            'reviews_count' => (clone $reviewStatsQuery)->count(),
        ];

        $highlightRoomIds = $homeSettings->highlightRoomIds();

        if ($highlightRoomIds !== []) {
            $highlightRooms = Room::query()
                ->where('is_published', true)
                ->whereIn('id', $highlightRoomIds)
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->get()
                ->sortBy(fn (Room $room): int => array_search($room->id, $highlightRoomIds, true))
                ->values();
        } else {
            $highlightRooms = Room::query()
                ->where('is_published', true)
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->orderByRaw('"reviews_avg_rating" desc nulls last')
                ->orderByDesc('reviews_count')
                ->orderBy('nomor_kamar')
                ->limit(3)
                ->get();
        }

        $searchRooms = Room::query()
            ->where('is_published', true)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
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
                'rating' => round((float) ($room->reviews_avg_rating ?? 0), 1),
                'reviews' => (int) ($room->reviews_count ?? 0),
                'url' => route('rooms.show', ['room' => $room->nomor_kamar]),
            ])
            ->values();

        return view('pages.home', compact('homeSettings', 'highlightRooms', 'guestProfile', 'homeStats', 'searchRooms'));
    }
}
