<?php

use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about', [AboutController::class, 'index'])->name('about');

Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::get('/gallery', function () {
    return view('pages.gallery');
})->name('gallery');

/* ─── AUTH — Sign Up (CRUD User) ─── */
Route::get('/signup', function () {
    return view('pages.signup');
})->name('signup');

Route::post('/signup', [UserController::class, 'store'])->name('signup.store');

Route::get('/login', function () {
    return view('pages.login');
})->name('login');

Route::post('/login', [UserController::class, 'login'])->name('login.store');

/* ─── PROFILE — Read, Update, Delete ─── */
Route::get('/profile', [UserController::class, 'show'])->name('profile');
Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
Route::delete('/profile', [UserController::class, 'destroy'])->name('profile.destroy');

/* ─── LOGOUT ─── */
Route::post('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');