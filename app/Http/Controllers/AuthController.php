<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View
    {
        if ($request->filled('redirect')) {
            $request->session()->put('url.intended', $request->string('redirect')->toString());
        }

        return view('pages.login');
    }

    public function showRegister(): View
    {
        return view('pages.signup');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
            'phone'    => ['required', 'string', 'max:20', Rule::unique('users', 'no_telepon')],
            'ktp'      => ['required', 'string', 'max:20', Rule::unique('users', 'no_ktp')],
        ], [
            'email.unique' => 'Post-el sudah terdaftar.',
            'phone.unique' => 'Nomor telepon sudah terdaftar. Silakan masuk atau gunakan nomor lain.',
            'ktp.unique' => 'Nomor KTP sudah terdaftar.',
        ]);

        $user = User::create([
            'nama_lengkap' => $data['name'],
            'email'    => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'no_telepon' => $data['phone'],
            'no_ktp' => $data['ktp'],
            'role' => 'penyewa',
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'Post-el atau kata sandi salah.'])
                ->onlyInput('email');
        }

        if (! Auth::user()?->is_active) {
            Auth::logout();

            return back()
                ->withErrors(['email' => 'Akun ini belum aktif.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
