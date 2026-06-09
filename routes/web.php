<?php

use App\Http\Controllers\RoomController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Models\Booking;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', [AboutController::class, 'index'])->name('about');

Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room:nomor_kamar}/booking', [RoomController::class, 'createBooking'])->name('rooms.booking.create');
Route::post('/rooms/{room:nomor_kamar}/booking', [RoomController::class, 'storeBooking'])->name('rooms.booking.store');
Route::post('/rooms/{room:nomor_kamar}/reviews', [RoomController::class, 'storeReview'])
    ->middleware('auth')
    ->name('rooms.reviews.store');
Route::get('/rooms/{room:nomor_kamar}', [RoomController::class, 'show'])->name('rooms.show');

Route::get('/contact', [ContactController::class, 'index'])
    ->name('contact');

Route::post('/contact', [ContactController::class, 'store'])
    ->name('contact.store');

Route::post('/contact/feedback', [ContactController::class, 'storeFeedback'])
    ->name('contact.feedback.store');

Route::get('/signup', [AuthController::class, 'showRegister'])->name('signup');

Route::post('/signup', [AuthController::class, 'register'])->name('signup.store');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/profile', function () {
    $bookings = Booking::query()
        ->with('room')
        ->where('user_id', auth()->id())
        ->latest()
        ->get();

    return view('pages.profile', compact('bookings'));
})->middleware('auth')->name('profile');
