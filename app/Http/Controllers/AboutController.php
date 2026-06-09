<?php

namespace App\Http\Controllers;

use App\Models\GuestProfile;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        return view('pages.about', [
            'guestProfile' => GuestProfile::active(),
        ]);
    }
}
