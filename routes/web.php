<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('index');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard routes
Route::get('/dashboard', function () { return view('admin.dashboard'); })->middleware('auth');
Route::get('/user-dashboard', function () { return view('user.dashboard'); })->middleware('auth');

use App\Http\Controllers\UserController;

// User section pages
Route::get('/user-accounts', function () { return view('user.accounts'); })->middleware('auth');
Route::get('/user-deposit', function () { return view('user.deposit'); })->middleware('auth');
Route::get('/user-loan', function () { return view('user.loan'); })->middleware('auth');

Route::get('/user-profile', [UserController::class, 'showProfile'])->middleware('auth');
Route::post('/user-profile/update', [UserController::class, 'updateProfile'])->middleware('auth');
Route::post('/user-profile/password', [UserController::class, 'updatePassword'])->middleware('auth');

Route::get('/user-transactions', function () { return view('user.transactions'); })->middleware('auth');
Route::get('/user-transfer', function () { return view('user.transfer'); })->middleware('auth');
