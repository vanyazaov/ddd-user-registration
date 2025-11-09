<?php

use App\Domain\User\ValueObjects\Password;
use Illuminate\Support\Facades\Route;
use App\UI\WEB\Controllers\RegisterController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', [RegisterController::class, 'showForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::get('/register/success', [RegisterController::class, 'success'])->name('register.success');
