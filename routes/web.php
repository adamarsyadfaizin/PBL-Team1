<?php

use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('pages.home'))->name('home');

Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');

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