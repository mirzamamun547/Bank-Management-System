<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('index');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard routes

Route::get('/dashboard', [AdminController::class, 'dashboard'])->middleware('auth')->name('admin.dashboard');
Route::post('/admin/approve-account/{id}', [AdminController::class, 'approveAccount'])->middleware('auth')->name('admin.approveAccount');
Route::post('/admin/approve-loan/{id}', [AdminController::class, 'approveLoan'])->middleware('auth')->name('admin.approveLoan');

// Deposit routes
Route::post('/admin/deposit/search',  [AdminController::class, 'depositSearch'])->middleware('auth')->name('admin.deposit.search');
Route::post('/admin/deposit/otp',     [AdminController::class, 'depositGenerateOtp'])->middleware('auth')->name('admin.deposit.otp');
Route::post('/admin/deposit/verify',  [AdminController::class, 'depositVerifyOtp'])->middleware('auth')->name('admin.deposit.verify');

// Withdraw routes
Route::post('/admin/withdraw/search', [AdminController::class, 'withdrawSearch'])->middleware('auth')->name('admin.withdraw.search');
Route::post('/admin/withdraw/otp',    [AdminController::class, 'withdrawGenerateOtp'])->middleware('auth')->name('admin.withdraw.otp');
Route::post('/admin/withdraw/verify', [AdminController::class, 'withdrawVerifyOtp'])->middleware('auth')->name('admin.withdraw.verify');

// Transfer routes
Route::post('/admin/transfer/search', [AdminController::class, 'transferSearch'])->middleware('auth')->name('admin.transfer.search');
Route::post('/admin/transfer/otp',    [AdminController::class, 'transferGenerateOtp'])->middleware('auth')->name('admin.transfer.otp');
Route::post('/admin/transfer/verify', [AdminController::class, 'transferVerifyOtp'])->middleware('auth')->name('admin.transfer.verify');

Route::get('/user-dashboard', [UserController::class, 'dashboard'])->middleware('auth')->name('user.dashboard');

// User section pages
Route::get('/user-accounts', [UserController::class, 'indexAccounts'])->middleware('auth')->name('user.accounts');
Route::post('/user-accounts', [UserController::class, 'storeAccount'])->middleware('auth');
Route::get('/user-loan', [UserController::class, 'indexLoans'])->middleware('auth')->name('user.loan');
Route::post('/user-loan/apply', [UserController::class, 'applyLoan'])->middleware('auth')->name('user.loan.apply');
Route::post('/user-loan/pay', [UserController::class, 'payLoanEmi'])->middleware('auth')->name('user.loan.pay');

Route::get('/user-profile', [UserController::class, 'showProfile'])->middleware('auth');
Route::post('/user-profile/update', [UserController::class, 'updateProfile'])->middleware('auth');
Route::post('/user-profile/password', [UserController::class, 'updatePassword'])->middleware('auth');
Route::get('/user-notifications', [UserController::class, 'notifications'])->middleware('auth')->name('user.notifications');

Route::get('/user-transactions', [UserController::class, 'indexTransactions'])->middleware('auth')->name('user.transactions');
Route::get('/user-transfer', [UserController::class, 'indexTransfer'])->middleware('auth')->name('user.transfer');
Route::post('/user-transfer', [UserController::class, 'storeTransfer'])->middleware('auth')->name('user.transfer.store');
Route::get('/customer/edit/{id}', [AdminController::class, 'editCustomer'])
    ->name('customer.edit');

Route::put('/customer/update/{id}', [AdminController::class, 'updateCustomer'])
    ->name('customer.update');

Route::delete('/customer/delete/{id}', [AdminController::class, 'deleteCustomer'])
    ->name('customer.delete');