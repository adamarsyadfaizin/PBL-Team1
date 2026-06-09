<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoomController extends Controller
{
    /**
     * Tampilkan halaman daftar semua kamar yang dipublikasikan.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $floor = trim((string) $request->query('lantai', ''));

        $baseQuery = Room::query()
            ->where('is_published', true);

        $totalRooms = (clone $baseQuery)->count();
        $availableRooms = (clone $baseQuery)
            ->where('status', 'tersedia')
            ->whereDoesntHave('bookings', $this->activeStayConstraint())
            ->count();
        $floors = (clone $baseQuery)
            ->orderBy('lantai')
            ->distinct()
            ->pluck('lantai')
            ->values();

        $rooms = $baseQuery
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('nomor_kamar', 'ilike', "%{$search}%")
                        ->orWhere('tipe_kamar', 'ilike', "%{$search}%")
                        ->orWhere('deskripsi', 'ilike', "%{$search}%")
                        ->orWhereRaw('fasilitas::text ilike ?', ["%{$search}%"]);
                });
            })
            ->when($status === 'tersedia', function ($query): void {
                $query
                    ->where('status', 'tersedia')
                    ->whereDoesntHave('bookings', $this->activeStayConstraint());
            })
            ->when($status === 'terisi', function ($query): void {
                $query->where(function ($query): void {
                    $query
                        ->where('status', 'terisi')
                        ->orWhereHas('bookings', $this->activeStayConstraint());
                });
            })
            ->when($status === 'perbaikan', function ($query): void {
                $query->where('status', 'perbaikan');
            })
            ->when($floor !== '', function ($query) use ($floor): void {
                $query->where('lantai', $floor);
            })
            ->withExists([
                'bookings as has_active_stay' => $this->activeStayConstraint(),
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderBy('lantai')
            ->orderBy('nomor_kamar')
            ->paginate(6)
            ->withQueryString();

        $rooms->getCollection()->transform(function (Room $room): Room {
            $room->setAttribute('display_status', $this->effectiveRoomStatus($room));

            return $room;
        });

        $recentRooms = $this->recentRooms($request);

        return view('pages.rooms.index', compact('rooms', 'search', 'status', 'floor', 'floors', 'totalRooms', 'availableRooms', 'recentRooms'));
    }

    public function show(Request $request, Room $room): View
    {
        abort_unless($room->is_published, 404);

        $room->loadCount('reviews')->loadAvg('reviews', 'rating');
        $this->rememberRoomView($request, $room);

        return view('pages.rooms.show', [
            'room' => $room,
            'availability' => $this->availabilityFor($room),
            'upcomingBookings' => $this->upcomingBookingsFor($room),
            'bookingCalendar' => $this->bookingCalendarFor($room),
            'mediaItems' => $this->mediaItemsFor($room),
            'reviews' => $room->reviews()
                ->with('user')
                ->latest()
                ->get(),
        ]);
    }

    public function storeReview(Request $request, Room $room): RedirectResponse
    {
        abort_unless($room->is_published, 404);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:1200'],
        ]);

        $room->reviews()->create([
            'user_id' => Auth::id(),
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);

        return redirect()
            ->route('rooms.show', ['room' => $room->nomor_kamar])
            ->with('review_success', 'Ulasan berhasil dikirim. Terima kasih atas masukan Anda.');
    }

    public function createBooking(Request $request, Room $room): View
    {
        abort_unless($room->is_published, 404);

        return view('pages.rooms.booking', [
            'room' => $room,
            'availability' => $this->availabilityFor($room),
            'upcomingBookings' => $this->upcomingBookingsFor($room),
            'bookingConflicts' => $this->bookingConflictPayloadFor($room),
            'needsDataStep' => $this->needsDataStep(),
            'bookingSuccess' => session('booking_success'),
            'trackedBooking' => $this->trackedBookingFor($request, $room),
        ]);
    }

    public function storeBooking(Request $request, Room $room): RedirectResponse
    {
        abort_unless($room->is_published, 404);

        $needsDataStep = $this->needsDataStep();
        $data = $request->validate([
            'nama' => [$needsDataStep ? 'required' : 'nullable', 'string', 'max:255'],
            'phone' => [$needsDataStep ? 'required' : 'nullable', 'string', 'max:30'],
            'email' => [$needsDataStep ? 'required' : 'nullable', 'email', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'tipe_sewa' => ['required', Rule::in(['harian', 'bulanan'])],
            'tanggal_check_in' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_check_out' => ['required', 'date', 'after:tanggal_check_in'],
            'catatan_penyewa' => ['nullable', 'string', 'max:1200'],
            'bukti_transfer' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
        ]);

        if (($room->status->value ?? $room->status) === 'perbaikan') {
            return back()
                ->withErrors(['tanggal_check_in' => 'Kamar ini sedang dalam perbaikan dan belum bisa dipesan.'])
                ->withInput()
                ->with('booking_step', $needsDataStep ? 2 : 1);
        }

        $checkIn = CarbonImmutable::parse($data['tanggal_check_in'])->startOfDay();
        $checkOut = CarbonImmutable::parse($data['tanggal_check_out'])->startOfDay();

        if ($this->hasBlockingBookingConflict($room, $checkIn, $checkOut)) {
            return back()
                ->withErrors(['tanggal_check_in' => 'Tanggal yang dipilih sudah difinalkan untuk penyewa lain dan tidak bisa dipesan.'])
                ->withInput()
                ->with('booking_step', $needsDataStep ? 2 : 1);
        }

        $price = $this->calculatePrice($room, $data['tipe_sewa'], $checkIn, $checkOut);

        try {
            $proofPath = $request->file('bukti_transfer')->store('booking-payment-proofs', 'public');

            $booking = DB::transaction(function () use ($data, $room, $checkIn, $checkOut, $price, $proofPath): Booking {
                $user = $this->bookingUser($data);
                $notes = trim((string) ($data['catatan_penyewa'] ?? ''));

                if (! empty($data['alamat'])) {
                    $notes = trim("Alamat penyewa: {$data['alamat']}\n\n{$notes}");
                }

                return Booking::create([
                    'kode_booking' => $this->newBookingCode(),
                    'user_id' => $user->id,
                    'room_id' => $room->id,
                    'tipe_sewa' => $data['tipe_sewa'],
                    'tanggal_check_in' => $checkIn->toDateString(),
                    'tanggal_check_out' => $checkOut->toDateString(),
                    'durasi' => $price['duration'],
                    'harga_snapshot' => $price['unit_price'],
                    'total_tagihan' => $price['total'],
                    'catatan_penyewa' => $notes !== '' ? $notes : null,
                    'bukti_transfer' => $proofPath,
                    'status' => 'menunggu_konfirmasi',
                ]);
            });
        } catch (QueryException $exception) {
            if (isset($proofPath)) {
                Storage::disk('public')->delete($proofPath);
            }

            report($exception);

            return back()
                ->withErrors(['tanggal_check_in' => 'Reservasi belum bisa dibuat. Silakan cek ulang tanggal atau hubungi admin.'])
                ->withInput()
                ->with('booking_step', $needsDataStep ? 2 : 1);
        }

        $request->session()->put('tracked_booking_id', $booking->id);

        return redirect()
            ->route('rooms.booking.create', ['room' => $room->nomor_kamar])
            ->with('booking_success', [
                'kode_booking' => $booking->kode_booking,
                'room' => $room->nomor_kamar,
                'check_in' => $checkIn->format('d/m/Y'),
                'check_out' => $checkOut->format('d/m/Y'),
                'total' => $price['total'],
                'date_warning' => $this->hasTentativeBookingConflict($room, $checkIn, $checkOut),
            ]);
    }

    private function trackedBookingFor(Request $request, Room $room): ?Booking
    {
        $bookingId = $request->session()->get('tracked_booking_id');

        if (! is_string($bookingId) || $bookingId === '') {
            return null;
        }

        return Booking::query()
            ->with(['room', 'confirmedBy'])
            ->whereKey($bookingId)
            ->where('room_id', $room->id)
            ->first();
    }

    private function availabilityFor(Room $room): ?object
    {
        $activeBooking = Booking::query()
            ->with('user')
            ->where('room_id', $room->id)
            ->whereIn('status', $this->blockingBookingStatuses())
            ->whereDate('tanggal_check_in', '<=', now()->toDateString())
            ->whereDate('tanggal_check_out', '>=', now()->toDateString())
            ->orderBy('tanggal_check_in')
            ->first();

        return (object) [
            'id' => $room->id,
            'nomor_kamar' => $room->nomor_kamar,
            'lantai' => $room->lantai,
            'harga_harian' => $room->harga_harian,
            'harga_bulanan' => $room->harga_bulanan,
            'status_kamar' => $activeBooking ? 'terisi' : ($room->status->value ?? $room->status),
            'booking_aktif_id' => $activeBooking?->id,
            'tipe_sewa' => $activeBooking?->tipe_sewa,
            'tanggal_check_in' => $activeBooking?->tanggal_check_in,
            'tanggal_check_out' => $activeBooking?->tanggal_check_out,
            'nama_penyewa_aktif' => $activeBooking?->user?->nama_lengkap,
            'telepon_penyewa_aktif' => $activeBooking?->user?->no_telepon,
        ];
    }

    /**
     * @return Collection<int, Booking>
     */
    private function upcomingBookingsFor(Room $room)
    {
        return Booking::query()
            ->with('user')
            ->where('room_id', $room->id)
            ->whereIn('status', $this->visibleBookingStatuses())
            ->whereDate('tanggal_check_out', '>=', now()->toDateString())
            ->orderBy('tanggal_check_in')
            ->get();
    }

    /**
     * @return array<int, array{label: string, days: list<array{date: ?string, number: string, in_month: bool, state: string, title: ?string}>}>
     */
    private function bookingCalendarFor(Room $room): array
    {
        $start = CarbonImmutable::now()->startOfMonth();
        $end = $start->addMonth()->endOfMonth();
        $bookings = Booking::query()
            ->with('user')
            ->where('room_id', $room->id)
            ->whereIn('status', $this->visibleBookingStatuses())
            ->whereDate('tanggal_check_in', '<=', $end->toDateString())
            ->whereDate('tanggal_check_out', '>=', $start->toDateString())
            ->orderBy('tanggal_check_in')
            ->get();

        return collect([0, 1])->map(function (int $offset) use ($start, $bookings): array {
            $monthStart = $start->addMonths($offset)->startOfMonth();
            $monthEnd = $monthStart->endOfMonth();
            $cursor = $monthStart->startOfWeek();
            $last = $monthEnd->endOfWeek();
            $days = [];

            while ($cursor->lessThanOrEqualTo($last)) {
                $dayBookings = $bookings->filter(fn (Booking $booking): bool => $this->bookingCoversDate($booking, $cursor));
                $blocking = $dayBookings->first(fn (Booking $booking): bool => in_array($booking->status, $this->blockingBookingStatuses(), true));
                $tentative = $dayBookings->first(fn (Booking $booking): bool => in_array($booking->status, $this->tentativeBookingStatuses(), true));
                $mainBooking = $blocking ?: $tentative;
                $state = $blocking ? 'blocked' : ($tentative ? 'tentative' : 'free');

                $days[] = [
                    'date' => $cursor->toDateString(),
                    'number' => $cursor->format('j'),
                    'in_month' => $cursor->month === $monthStart->month,
                    'state' => $state,
                    'title' => $mainBooking
                        ? $this->bookingStatusLabel($mainBooking->status).' - '.$mainBooking->tanggal_check_in->format('d/m/Y').' sampai '.$mainBooking->tanggal_check_out->format('d/m/Y')
                        : null,
                ];

                $cursor = $cursor->addDay();
            }

            return [
                'label' => $monthStart->translatedFormat('F Y'),
                'days' => $days,
            ];
        })->all();
    }

    /**
     * @return list<array{status: string, label: string, check_in: string, check_out: string, guest: ?string}>
     */
    private function bookingConflictPayloadFor(Room $room): array
    {
        return Booking::query()
            ->with('user')
            ->where('room_id', $room->id)
            ->whereIn('status', $this->visibleBookingStatuses())
            ->whereDate('tanggal_check_out', '>=', now()->toDateString())
            ->orderBy('tanggal_check_in')
            ->get()
            ->map(fn (Booking $booking): array => [
                'status' => $booking->status,
                'label' => $this->bookingStatusLabel($booking->status),
                'check_in' => $booking->tanggal_check_in->toDateString(),
                'check_out' => $booking->tanggal_check_out->toDateString(),
                'guest' => $booking->user?->nama_lengkap,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{type: string, src: string, label: string, mime?: string, poster?: string}>
     */
    private function mediaItemsFor(Room $room): array
    {
        $items = [];

        if ($room->foto_utama) {
            $items[] = [
                'type' => 'image',
                'src' => $this->storageUrl($room->foto_utama),
                'label' => "Foto utama kamar {$room->nomor_kamar}",
            ];
        }

        foreach ($room->gallery_images ?: [] as $index => $image) {
            if (! is_string($image) || blank($image)) {
                continue;
            }

            $items[] = [
                'type' => 'image',
                'src' => $this->storageUrl($image),
                'label' => 'Galeri kamar '.$room->nomor_kamar.' #'.($index + 1),
            ];
        }

        if ($room->video_path) {
            $items[] = [
                'type' => 'video',
                'src' => $this->storageUrl($room->video_path),
                'mime' => $this->videoMimeType($room->video_path),
                'poster' => $items[0]['src'] ?? null,
                'label' => "Video kamar {$room->nomor_kamar}",
            ];
        }

        return $items !== [] ? $items : [[
            'type' => 'image',
            'src' => $this->roomPlaceholder($room),
            'label' => "Placeholder kamar {$room->nomor_kamar}",
        ]];
    }

    private function storageUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        return '/storage/'.$path;
    }

    private function videoMimeType(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'webm' => 'video/webm',
            'ogg', 'ogv' => 'video/ogg',
            default => 'video/mp4',
        };
    }

    private function roomPlaceholder(Room $room): string
    {
        $label = htmlspecialchars("Kamar {$room->nomor_kamar}", ENT_QUOTES, 'UTF-8');
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1280" height="900" viewBox="0 0 1280 900" role="img" aria-label="{$label}">
  <rect width="1280" height="900" fill="#121212"/>
  <rect x="110" y="120" width="1060" height="660" rx="34" fill="#1b1b1b" stroke="#303030" stroke-width="4"/>
  <path d="M250 560h780v110H250zM305 445h280v115H305zM695 445h280v115H695z" fill="#252525" stroke="#3a3a3a" stroke-width="4"/>
  <path d="M250 380h780v180H250z" fill="#202020" stroke="#3a3a3a" stroke-width="4"/>
  <circle cx="640" cy="285" r="58" fill="#4361ee" opacity=".9"/>
  <text x="640" y="825" text-anchor="middle" fill="#f0f0f0" font-family="Arial, sans-serif" font-size="52" font-weight="700">{$label}</text>
</svg>
SVG;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    private function needsDataStep(): bool
    {
        $user = Auth::user();

        return ! $user
            || blank($user->name)
            || blank($user->email)
            || blank($user->phone);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function bookingUser(array $data): User
    {
        if ($user = Auth::user()) {
            $user->forceFill([
                'nama_lengkap' => $user->name ?: ($data['nama'] ?? null),
                'email' => $user->email ?: ($data['email'] ?? null),
                'no_telepon' => $user->phone ?: ($data['phone'] ?? null),
            ])->save();

            return $user;
        }

        $email = (string) $data['email'];
        $user = User::query()->where('email', $email)->first();

        if ($user) {
            return $user;
        }

        return User::create([
            'nama_lengkap' => $data['nama'],
            'email' => $email,
            'no_telepon' => $data['phone'],
            'password_hash' => Hash::make(Str::random(32)),
            'role' => 'penyewa',
            'is_active' => true,
        ]);
    }

    private function hasBlockingBookingConflict(Room $room, CarbonImmutable $checkIn, CarbonImmutable $checkOut): bool
    {
        return Booking::query()
            ->where('room_id', $room->id)
            ->whereIn('status', $this->blockingBookingStatuses())
            ->whereDate('tanggal_check_in', '<=', $checkOut->toDateString())
            ->whereDate('tanggal_check_out', '>=', $checkIn->toDateString())
            ->exists();
    }

    private function hasTentativeBookingConflict(Room $room, CarbonImmutable $checkIn, CarbonImmutable $checkOut): bool
    {
        return Booking::query()
            ->where('room_id', $room->id)
            ->whereIn('status', $this->tentativeBookingStatuses())
            ->whereDate('tanggal_check_in', '<=', $checkOut->toDateString())
            ->whereDate('tanggal_check_out', '>=', $checkIn->toDateString())
            ->exists();
    }

    /**
     * @return list<string>
     */
    private function blockingBookingStatuses(): array
    {
        return ['active_stay'];
    }

    /**
     * @return list<string>
     */
    private function tentativeBookingStatuses(): array
    {
        return ['pending', 'menunggu_konfirmasi'];
    }

    /**
     * @return list<string>
     */
    private function visibleBookingStatuses(): array
    {
        return array_merge($this->tentativeBookingStatuses(), $this->blockingBookingStatuses());
    }

    private function bookingCoversDate(Booking $booking, CarbonImmutable $date): bool
    {
        return $date->greaterThanOrEqualTo(CarbonImmutable::parse($booking->tanggal_check_in)->startOfDay())
            && $date->lessThanOrEqualTo(CarbonImmutable::parse($booking->tanggal_check_out)->startOfDay());
    }

    private function bookingStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Sudah diajukan / menunggu data',
            'menunggu_konfirmasi' => 'Sudah keluar / menunggu konfirmasi admin',
            'active_stay' => 'Sudah final / belum keluar',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    private function activeStayConstraint(): \Closure
    {
        return function ($query): void {
            $query
                ->whereIn('status', $this->blockingBookingStatuses())
                ->whereDate('tanggal_check_in', '<=', now()->toDateString())
                ->whereDate('tanggal_check_out', '>=', now()->toDateString());
        };
    }

    private function effectiveRoomStatus(Room $room): string
    {
        if ((bool) $room->getAttribute('has_active_stay')) {
            return 'terisi';
        }

        return (string) ($room->status->value ?? $room->status);
    }

    private function rememberRoomView(Request $request, Room $room): void
    {
        $ids = $request->session()->get('recent_room_ids', []);
        $ids = is_array($ids) ? $ids : [];
        $ids = array_values(array_filter(
            array_map(fn (mixed $id): string => (string) $id, $ids),
            fn (string $id): bool => $id !== $room->id,
        ));

        array_unshift($ids, $room->id);

        $request->session()->put('recent_room_ids', array_slice($ids, 0, 5));
    }

    /**
     * @return Collection<int, Room>
     */
    private function recentRooms(Request $request)
    {
        $ids = $request->session()->get('recent_room_ids', []);
        $ids = is_array($ids) ? array_values(array_filter($ids)) : [];

        if ($ids === []) {
            return collect();
        }

        return Room::query()
            ->where('is_published', true)
            ->whereIn('id', $ids)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->get()
            ->sortBy(fn (Room $room): int => array_search($room->id, $ids, true))
            ->values();
    }

    /**
     * @return array{duration: int, unit_price: float, total: float}
     */
    private function calculatePrice(Room $room, string $type, CarbonImmutable $checkIn, CarbonImmutable $checkOut): array
    {
        $days = max(1, (int) $checkIn->diffInDays($checkOut));
        $duration = $type === 'bulanan' ? max(1, (int) ceil($days / 30)) : $days;
        $unitPrice = (float) ($type === 'bulanan' ? $room->harga_bulanan : $room->harga_harian);
        $deposit = (float) $room->deposit;

        return [
            'duration' => $duration,
            'unit_price' => $unitPrice,
            'total' => ($unitPrice * $duration) + $deposit,
        ];
    }

    private function newBookingCode(): string
    {
        do {
            $code = 'BK'.now()->format('ymd').strtoupper(Str::random(6));
        } while (Booking::query()->where('kode_booking', $code)->exists());

        return $code;
    }
}
