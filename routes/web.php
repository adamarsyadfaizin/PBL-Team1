<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutController;

Route::get('/', function () {
    return view('home');
});
Route::get('/about', [App\Http\Controllers\AboutController::class, 'index'])->name('about');