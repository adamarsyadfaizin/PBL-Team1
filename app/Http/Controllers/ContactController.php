<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuestRequest;
use App\Models\Room;

class ContactController extends Controller
{
    /**
     * Display contact page.
     */
    public function index()
    {
        $rooms = Room::all();

        return view('pages.contact', compact('rooms'));
    }

    /**
     * Store guest request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|max:255',
            'phone' => 'required|max:50',
            'email' => 'nullable|email',

            'request_type' => 'required',

            'checkin' => 'nullable|date',
            'checkout' => 'nullable|date',

            'tipe_kamar' => 'nullable|max:255',
            'tipe_sewa' => 'nullable|max:255',
            'jumlah_tamu' => 'nullable|max:255',

            'complaint_category' => 'nullable|max:255',

            'pesan' => 'nullable',
        ]);

        GuestRequest::create($validated);

        return redirect()
            ->back()
            ->with('success', 'Permintaan berhasil dikirim!');
    }
}