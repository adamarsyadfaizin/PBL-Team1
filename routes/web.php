<?php

use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');

Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');