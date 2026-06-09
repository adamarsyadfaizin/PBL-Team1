<?php

namespace App\Http\Controllers;

use App\Models\GuestProfile;
use App\Models\GuestRequest;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    /**
     * Display contact page.
     */
    public function index()
    {
        $rooms = Room::query()
            ->where('is_published', true)
            ->orderBy('lantai')
            ->orderBy('nomor_kamar')
            ->get();
        $guestProfile = GuestProfile::active();

        return view('pages.contact', compact('rooms', 'guestProfile'));
    }

    /**
     * Store guest request.
     */
    public function store(Request $request)
    {
        $requestType = (string) $request->input('request_type');
        $needsStayDetails = in_array($requestType, ['availability', 'booking'], true);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],

            'request_type' => ['required', Rule::in(['availability', 'booking', 'reschedule', 'cancel', 'complaint', 'general'])],

            'checkin' => [Rule::requiredIf($needsStayDetails), 'nullable', 'date'],
            'checkout' => [Rule::requiredIf($needsStayDetails), 'nullable', 'date', 'after_or_equal:checkin'],

            'tipe_kamar' => [Rule::requiredIf($needsStayDetails), 'nullable', 'string', 'max:255', Rule::exists('rooms', 'nomor_kamar')],
            'tipe_sewa' => [Rule::requiredIf($needsStayDetails), 'nullable', Rule::in(['harian', 'bulanan'])],
            'jumlah_tamu' => ['nullable', 'string', 'max:255'],

            'complaint_category' => ['nullable', 'string', 'max:255'],
            'manage_reason' => ['nullable', 'string', 'max:1200'],

            'pesan' => ['nullable', 'string', 'max:2000'],
        ]);

        if (! empty($validated['manage_reason'])) {
            $prefix = in_array($requestType, ['reschedule', 'cancel'], true)
                ? 'Alasan perubahan/pembatalan'
                : 'Alasan';

            $validated['pesan'] = trim($prefix.": {$validated['manage_reason']}\n\n".($validated['pesan'] ?? ''));
        }

        unset($validated['manage_reason']);

        GuestRequest::create($validated);

        return redirect()
            ->back()
            ->with('success', 'Permintaan berhasil dikirim!');
    }

    public function storeFeedback(Request $request)
    {
        $validated = $request->validate([
            'masukkan' => ['required', 'string', 'max:2000'],
        ]);

        $profile = GuestProfile::query()->first()
            ?? GuestProfile::query()->create(GuestProfile::defaults());

        $masukkan = $profile->masukkan ?: [];
        $masukkan[] = [
            'message' => $validated['masukkan'],
            'created_at' => now()->toDateTimeString(),
        ];

        $profile->update(['masukkan' => $masukkan]);

        return redirect()
            ->route('contact')
            ->with('feedback_success', 'Terima kasih, masukan Anda sudah kami terima.');
    }
}
