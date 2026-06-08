<?php

use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutController;

use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', [AboutController::class, 'index'])->name('about');

Route::get('/rooms', [App\Http\Controllers\RoomController::class, 'index'])->name('rooms.index');

// Rute sementara untuk detail kamar karena belum ada RoomController@show dan view-nya
Route::get('/rooms/{room}', function ($room) {
    return redirect()->route('rooms.index');
})->name('rooms.show');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::get('/gallery', function () {
    return view('pages.gallery');
})->name('gallery');

Route::get('/signup', function () {
    return view('pages.signup');
})->name('signup');

Route::get('/login', function () {
    return view('pages.login');
})->name('login');

Route::get('/profile', function () {
    return view('pages.profile');
})->name('profile');