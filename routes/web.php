<?php

use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');

Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/gallery', function () {
    return view('gallery');
})->name('gallery');

Route::get('/signup', function () {
    return view('signup');
})->name('signup');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/profile', function () {
    return view('profile');
})->name('profile');