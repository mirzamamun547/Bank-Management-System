<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('index');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Employee Dashboard routes
Route::get('/employee-dashboard', [EmployeeController::class, 'dashboard'])->middleware('auth')->name('employee.dashboard');
Route::post('/employee/approve-account/{id}', [EmployeeController::class, 'approveAccount'])->middleware('auth')->name('employee.approveAccount');
Route::post('/employee/approve-loan/{id}', [EmployeeController::class, 'approveLoan'])->middleware('auth')->name('employee.approveLoan');

// Employee Deposit routes
Route::post('/employee/deposit/search',  [EmployeeController::class, 'depositSearch'])->middleware('auth')->name('employee.deposit.search');
Route::post('/employee/deposit/otp',     [EmployeeController::class, 'depositGenerateOtp'])->middleware('auth')->name('employee.deposit.otp');
Route::post('/employee/deposit/verify',  [EmployeeController::class, 'depositVerifyOtp'])->middleware('auth')->name('employee.deposit.verify');

// Employee Withdraw routes
Route::post('/employee/withdraw/search', [EmployeeController::class, 'withdrawSearch'])->middleware('auth')->name('employee.withdraw.search');
Route::post('/employee/withdraw/otp',    [EmployeeController::class, 'withdrawGenerateOtp'])->middleware('auth')->name('employee.withdraw.otp');
Route::post('/employee/withdraw/verify', [EmployeeController::class, 'withdrawVerifyOtp'])->middleware('auth')->name('employee.withdraw.verify');

// Employee Transfer routes
Route::post('/employee/transfer/search', [EmployeeController::class, 'transferSearch'])->middleware('auth')->name('employee.transfer.search');
Route::post('/employee/transfer/otp',    [EmployeeController::class, 'transferGenerateOtp'])->middleware('auth')->name('employee.transfer.otp');
Route::post('/employee/transfer/verify', [EmployeeController::class, 'transferVerifyOtp'])->middleware('auth')->name('employee.transfer.verify');

// Admin Dashboard and Management routes (Explicitly Defined)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin-dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/customers', [AdminController::class, 'customers'])->name('admin.customers');
    Route::post('/admin/customers/{id}/suspend', [AdminController::class, 'suspendCustomer'])->name('admin.customers.suspend');
    Route::get('/admin/employees', [AdminController::class, 'employees'])->name('admin.employees');
    Route::post('/admin/employees/store', [AdminController::class, 'storeEmployee'])->name('admin.employees.store');
    Route::post('/admin/employees/{id}/toggle', [AdminController::class, 'toggleEmployeeStatus'])->name('admin.employees.toggle');
    Route::get('/admin/branches', [BranchController::class, 'index'])->name('admin.branches');
    Route::post('/admin/branches/store', [BranchController::class, 'store'])->name('admin.branches.store');
    Route::post('/admin/branches/{id}/update', [BranchController::class, 'update'])->name('admin.branches.update');
    Route::post('/admin/branches/{id}/delete', [BranchController::class, 'destroy'])->name('admin.branches.delete');
    Route::get('/admin/accounts', [AdminController::class, 'accounts'])->name('admin.accounts');
    Route::get('/admin/loans', [AdminController::class, 'loans'])->name('admin.loans');
    Route::post('/admin/loans/{id}/approve', [AdminController::class, 'approveLoan'])->name('admin.loans.approve');
    Route::post('/admin/loans/{id}/reject', [AdminController::class, 'rejectLoan'])->name('admin.loans.reject');
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports');
    Route::get('/admin/audit-logs', [AdminController::class, 'auditLogs'])->name('admin.audit');
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/admin/settings/update', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
});

// User section pages
Route::get('/user-dashboard', [UserController::class, 'dashboard'])->middleware('auth')->name('user.dashboard');
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

Route::get('/employee/customer/edit/{id}', [EmployeeController::class, 'editCustomer'])
    ->name('employee.customer.edit');

Route::put('/employee/customer/update/{id}', [EmployeeController::class, 'updateCustomer'])
    ->name('employee.customer.update');

Route::delete('/employee/customer/delete/{id}', [EmployeeController::class, 'deleteCustomer'])
    ->name('employee.customer.delete');