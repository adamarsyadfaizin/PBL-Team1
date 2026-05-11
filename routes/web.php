<?php

use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/about', [AboutController::class, 'index'])->name('about');

Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');

Route::get('/contact', [ContactController::class, 'index'])
    ->name('contact');

Route::post('/contact', [ContactController::class, 'store'])
    ->name('contact.store');

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