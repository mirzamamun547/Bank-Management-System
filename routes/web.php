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
use App\Http\Controllers\AdminController;

Route::get('/dashboard', [AdminController::class, 'dashboard'])->middleware('auth')->name('admin.dashboard');
Route::post('/admin/approve-account/{id}', [AdminController::class, 'approveAccount'])->middleware('auth')->name('admin.approveAccount');
Route::get('/user-dashboard', function () { return view('user.dashboard'); })->middleware('auth');

use App\Http\Controllers\UserController;

// User section pages
Route::get('/user-accounts', [UserController::class, 'indexAccounts'])->middleware('auth')->name('user.accounts');
Route::post('/user-accounts', [UserController::class, 'storeAccount'])->middleware('auth');
Route::get('/user-deposit', function () { return view('user.deposit'); })->middleware('auth');
Route::get('/user-loan', function () { return view('user.loan'); })->middleware('auth');

Route::get('/user-profile', [UserController::class, 'showProfile'])->middleware('auth');
Route::post('/user-profile/update', [UserController::class, 'updateProfile'])->middleware('auth');
Route::post('/user-profile/password', [UserController::class, 'updatePassword'])->middleware('auth');
Route::get('/user-notifications', [UserController::class, 'notifications'])->middleware('auth')->name('user.notifications');

Route::get('/user-transactions', function () { return view('user.transactions'); })->middleware('auth');
Route::get('/user-transfer', function () { return view('user.transfer'); })->middleware('auth');
