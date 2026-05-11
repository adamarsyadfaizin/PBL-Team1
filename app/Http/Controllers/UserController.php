<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /* ─────────────────────────────────────────
     |  LOGIN — Proses autentikasi user
     ───────────────────────────────────────── */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('profile'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ]);
    }

    /* ─────────────────────────────────────────
     |  CREATE — Proses registrasi user baru
     ───────────────────────────────────────── */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::min(8)->letters()->numbers(), 'confirmed'],
            'phone'    => ['required', 'string', 'max:20'],
            'ktp'      => ['required', 'string', 'size:16', 'regex:/^[0-9]+$/'],
        ], [
            'name.required'         => 'Nama lengkap wajib diisi.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Email ini sudah terdaftar. Silakan gunakan email lain.',
            'password.required'     => 'Password wajib diisi.',
            'password.min'          => 'Password minimal 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
            'phone.required'        => 'Nomor telepon wajib diisi.',
            'ktp.required'          => 'Nomor KTP wajib diisi.',
            'ktp.size'              => 'Nomor KTP harus 16 digit.',
            'ktp.regex'             => 'Nomor KTP hanya boleh berisi angka.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'user',
            'phone'    => $validated['phone'],
            'ktp'      => $validated['ktp'],
        ]);

        // Login otomatis setelah registrasi berhasil
        Auth::login($user);

        return redirect()->route('profile')
            ->with('success', 'Selamat datang, ' . $user->name . '! Akun Anda berhasil dibuat.');
    }

    /* ─────────────────────────────────────────
     |  READ — Tampilkan profil user
     ───────────────────────────────────────── */
    public function show(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('pages.profile', ['user' => Auth::user()]);
    }

    /* ─────────────────────────────────────────
     |  UPDATE (form) — Tampilkan form edit profil
     ───────────────────────────────────────── */
    public function edit(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('pages.profile-edit', ['user' => Auth::user()]);
    }

    /* ─────────────────────────────────────────
     |  UPDATE (proses) — Simpan perubahan profil
     ───────────────────────────────────────── */
    public function update(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $rules = [
            'name'  => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'ktp'   => ['required', 'string', 'size:16', 'regex:/^[0-9]+$/'],
        ];

        // Validasi password hanya jika diisi
        if ($request->filled('password')) {
            $rules['password']              = ['required', Password::min(8)->letters()->numbers(), 'confirmed'];
            $rules['password_confirmation'] = ['required'];
        }

        $validated = $request->validate($rules, [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'phone.required'     => 'Nomor telepon wajib diisi.',
            'ktp.required'       => 'Nomor KTP wajib diisi.',
            'ktp.size'           => 'Nomor KTP harus 16 digit.',
            'ktp.regex'          => 'Nomor KTP hanya boleh berisi angka.',
            'password.min'       => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $updateData = [
            'name'  => $validated['name'],
            'phone' => $validated['phone'],
            'ktp'   => $validated['ktp'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('profile')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /* ─────────────────────────────────────────
     |  DELETE — Hapus akun user
     ───────────────────────────────────────── */
    public function destroy(): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        Auth::logout();
        $user->delete();

        return redirect()->route('home')
            ->with('success', 'Akun Anda telah berhasil dihapus.');
    }
}
