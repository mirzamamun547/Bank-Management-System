<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Account;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $pendingAccounts = Account::with('user')->where('status', 'Pending')->get();
        $activeAccounts  = Account::with('user')->where('status', '!=', 'Pending')->get();
        $customers = DB::table('USERS')
            ->select('id', 'customer_id', 'first_name', 'last_name', 'phone', 'address')
            ->where('role', 'CUSTOMER')
            ->orderBy('customer_id')
            ->get();

        return view('admin.dashboard', compact('pendingAccounts', 'activeAccounts', 'customers'));
    }

    public function editCustomer($id)
    {
        $customer = DB::table('USERS')->where('id', $id)->first();
        if (!$customer) { abort(404, 'Customer not found.'); }
        return view('admin.edit-customer', compact('customer'));
    }

    public function updateCustomer(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'email'      => 'required|email|max:100|unique:USERS,EMAIL,' . $id,
            'phone'      => 'nullable|string|max:20',
            'nid'        => 'nullable|string|max:50',
            'address'    => 'nullable|string|max:255',
            'dob'        => 'nullable|date',
            'status'     => 'required|string|in:ACTIVE,SUSPENDED',
        ]);
        DB::table('USERS')->where('id', $id)->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'full_name'  => $request->first_name . ' ' . $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'nid'        => $request->nid,
            'address'    => $request->address,
            'dob'        => $request->dob ? Carbon::parse($request->dob) : null,
            'status'     => $request->status,
            'updated_at' => now(),
        ]);
        return redirect('/dashboard')->with('success', 'Customer profile updated successfully.');
    }

    public function deleteCustomer($id)
    {
        DB::table('USERS')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Customer deleted successfully.');
    }

    public function approveAccount(Request $request, $id)
    {
        $account = Account::with('user')->findOrFail($id);
        if ($account->status !== 'Pending') {
            return back()->with('error', 'Account is not in a pending state.');
        }
        $pin = mt_rand(1000, 9999);
        DB::transaction(function () use ($account, $pin) {
            $account->status = 'Active';
            $account->save();
            DB::table('notifications')->insert([
                'user_id'    => $account->user_id,
                'message'    => "Your bank account request for {$account->account_type} has been approved. Account No: {$account->account_number}, PIN: {$pin}",
                'is_read'    => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
        return back()->with('success', 'Account approved successfully. Notification sent to customer.');
    }

    // =========================================================
    //  HELPERS
    // =========================================================
    private function findActiveAccount(string $accountNumber)
    {
        return DB::table('accounts')
            ->join('USERS', 'accounts.user_id', '=', 'USERS.id')
            ->select(
                'accounts.id as account_id',
                'accounts.account_number',
                'accounts.balance',
                'USERS.full_name',
                'USERS.nid',
                'USERS.id as user_id'
            )
            ->where('accounts.account_number', $accountNumber)
            ->where('accounts.status', 'Active')
            ->first();
    }

    private function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    // =========================================================
    //  DEPOSIT FLOW
    // =========================================================
    public function depositSearch(Request $request)
    {
        $request->validate(['account_number' => 'required|string', 'nid' => 'required|string']);
        $account = $this->findActiveAccount($request->account_number);
        if (!$account || strtolower($account->nid) !== strtolower($request->nid)) {
            return redirect('/dashboard')->with('deposit_error', '❌ Invalid Account Number or NID. Please try again.');
        }
        return redirect('/dashboard')->with('deposit_customer', [
            'account_id' => $account->account_id, 'account_number' => $account->account_number,
            'full_name'  => $account->full_name,  'balance'        => $account->balance,
            'user_id'    => $account->user_id,
        ]);
    }

    public function depositGenerateOtp(Request $request)
    {
        $request->validate(['account_number' => 'required|string', 'amount' => 'required|numeric|min:0.01']);
        $account = $this->findActiveAccount($request->account_number);
        if (!$account) {
            return redirect('/dashboard')->with('deposit_error', '❌ Account not found.');
        }
        $otp   = $this->generateOtp();
        $otpId = DB::table('otp_verification')->insertGetId([
            'account_number' => $request->account_number,
            'amount'         => $request->amount,
            'otp'            => $otp,
            'type'           => 'DEPOSIT',
            'expires_at'     => Carbon::now()->addMinutes(5),
            'created_at'     => now(), 'updated_at' => now(),
        ]);
        DB::table('notifications')->insert([
            'user_id'    => $account->user_id,
            'message'    => "🔐 Deposit Verification OTP: {$otp}\nAmount: $" . number_format($request->amount, 2) . "\nValid for 5 minutes.",
            'is_read'    => 0, 'created_at' => now(), 'updated_at' => now(),
        ]);
        return redirect('/dashboard')
            ->with('deposit_otp_id', $otpId)
            ->with('deposit_customer', [
                'account_id' => $account->account_id, 'account_number' => $account->account_number,
                'full_name'  => $account->full_name,  'balance'        => $account->balance,
                'user_id'    => $account->user_id,
            ]);
    }

    public function depositVerifyOtp(Request $request)
    {
        $request->validate(['otp_id' => 'required|integer', 'otp' => 'required|string|size:6']);
        $record = DB::table('otp_verification')->where('id', $request->otp_id)->first();
        if (!$record) {
            return redirect('/dashboard')->with('deposit_error', '❌ OTP session not found. Please start again.');
        }
        if (Carbon::parse($record->expires_at)->isPast()) {
            DB::table('otp_verification')->where('id', $record->id)->delete();
            return redirect('/dashboard')->with('deposit_error', '❌ OTP Expired. Please generate a new one.');
        }
        if ($record->otp !== $request->otp) {
            return redirect('/dashboard')
                ->with('deposit_error', '❌ Invalid OTP. Please try again.')
                ->with('deposit_otp_id', $record->id);
        }
        DB::transaction(function () use ($record) {
            DB::table('accounts')->where('account_number', $record->account_number)->increment('balance', $record->amount);
            $account = DB::table('accounts')
                ->join('USERS', 'accounts.user_id', '=', 'USERS.id')
                ->select('accounts.id as account_id', 'accounts.balance', 'USERS.id as user_id')
                ->where('accounts.account_number', $record->account_number)->first();
            DB::table('transactions')->insert([
                'account_id' => $account->account_id, 'transaction_type' => 'DEPOSIT',
                'amount' => $record->amount, 'description' => 'Cash deposit processed by bank employee.',
                'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('notifications')->insert([
                'user_id' => $account->user_id,
                'message' => "✅ Deposit Successful!\nAmount: $" . number_format($record->amount, 2) . "\nNew Balance: $" . number_format($account->balance, 2),
                'is_read' => 0, 'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('audit_log')->insert([
                'table_name' => 'accounts', 'action' => 'DEPOSIT',
                'performed_by' => auth()->user()->full_name ?? 'Employee',
                'details' => "Deposited $" . number_format($record->amount, 2) . " to account {$record->account_number}",
                'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('otp_verification')->where('id', $record->id)->delete();
        });
        return redirect('/dashboard')->with('deposit_success', '✅ Deposit of $' . number_format($record->amount, 2) . ' completed successfully!');
    }

    // =========================================================
    //  WITHDRAW FLOW
    // =========================================================
    public function withdrawSearch(Request $request)
    {
        $request->validate(['account_number' => 'required|string', 'nid' => 'required|string']);
        $account = $this->findActiveAccount($request->account_number);
        if (!$account || strtolower($account->nid) !== strtolower($request->nid)) {
            return redirect('/dashboard')->with('withdraw_error', '❌ Invalid Account Number or NID. Please try again.');
        }
        return redirect('/dashboard')->with('withdraw_customer', [
            'account_id' => $account->account_id, 'account_number' => $account->account_number,
            'full_name'  => $account->full_name,  'balance'        => $account->balance,
            'user_id'    => $account->user_id,
        ]);
    }

    public function withdrawGenerateOtp(Request $request)
    {
        $request->validate(['account_number' => 'required|string', 'amount' => 'required|numeric|min:0.01']);
        $account = $this->findActiveAccount($request->account_number);
        if (!$account) {
            return redirect('/dashboard')->with('withdraw_error', '❌ Account not found.');
        }
        if ($account->balance < $request->amount) {
            return redirect('/dashboard')
                ->with('withdraw_error', '❌ Insufficient balance. Available: $' . number_format($account->balance, 2))
                ->with('withdraw_customer', [
                    'account_id' => $account->account_id, 'account_number' => $account->account_number,
                    'full_name'  => $account->full_name,  'balance'        => $account->balance,
                    'user_id'    => $account->user_id,
                ]);
        }
        $otp   = $this->generateOtp();
        $otpId = DB::table('otp_verification')->insertGetId([
            'account_number' => $request->account_number,
            'amount'         => $request->amount,
            'otp'            => $otp,
            'type'           => 'WITHDRAW',
            'expires_at'     => Carbon::now()->addMinutes(5),
            'created_at'     => now(), 'updated_at' => now(),
        ]);
        DB::table('notifications')->insert([
            'user_id'    => $account->user_id,
            'message'    => "🔐 Withdrawal Verification OTP: {$otp}\nAmount: $" . number_format($request->amount, 2) . "\nValid for 5 minutes.",
            'is_read'    => 0, 'created_at' => now(), 'updated_at' => now(),
        ]);
        return redirect('/dashboard')
            ->with('withdraw_otp_id', $otpId)
            ->with('withdraw_customer', [
                'account_id' => $account->account_id, 'account_number' => $account->account_number,
                'full_name'  => $account->full_name,  'balance'        => $account->balance,
                'user_id'    => $account->user_id,
            ]);
    }

    public function withdrawVerifyOtp(Request $request)
    {
        $request->validate(['otp_id' => 'required|integer', 'otp' => 'required|string|size:6']);
        $record = DB::table('otp_verification')->where('id', $request->otp_id)->first();
        if (!$record) {
            return redirect('/dashboard')->with('withdraw_error', '❌ OTP session not found. Please start again.');
        }
        if (Carbon::parse($record->expires_at)->isPast()) {
            DB::table('otp_verification')->where('id', $record->id)->delete();
            return redirect('/dashboard')->with('withdraw_error', '❌ OTP Expired. Please generate a new one.');
        }
        if ($record->otp !== $request->otp) {
            return redirect('/dashboard')
                ->with('withdraw_error', '❌ Invalid OTP. Please try again.')
                ->with('withdraw_otp_id', $record->id);
        }
        DB::transaction(function () use ($record) {
            DB::table('accounts')->where('account_number', $record->account_number)->decrement('balance', $record->amount);
            $account = DB::table('accounts')
                ->join('USERS', 'accounts.user_id', '=', 'USERS.id')
                ->select('accounts.id as account_id', 'accounts.balance', 'USERS.id as user_id')
                ->where('accounts.account_number', $record->account_number)->first();
            DB::table('transactions')->insert([
                'account_id' => $account->account_id, 'transaction_type' => 'WITHDRAW',
                'amount' => $record->amount, 'description' => 'Cash withdrawal processed by bank employee.',
                'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('notifications')->insert([
                'user_id' => $account->user_id,
                'message' => "✅ Withdrawal Successful!\nAmount: $" . number_format($record->amount, 2) . "\nNew Balance: $" . number_format($account->balance, 2),
                'is_read' => 0, 'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('audit_log')->insert([
                'table_name' => 'accounts', 'action' => 'WITHDRAW',
                'performed_by' => auth()->user()->full_name ?? 'Employee',
                'details' => "Withdrew $" . number_format($record->amount, 2) . " from account {$record->account_number}",
                'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('otp_verification')->where('id', $record->id)->delete();
        });
        return redirect('/dashboard')->with('withdraw_success', '✅ Withdrawal of $' . number_format($record->amount, 2) . ' completed successfully!');
    }

    // =========================================================
    //  TRANSFER FLOW
    // =========================================================
    public function transferSearch(Request $request)
    {
        $request->validate(['from_account' => 'required|string', 'to_account' => 'required|string', 'nid' => 'required|string']);
        $fromAccount = $this->findActiveAccount($request->from_account);
        if (!$fromAccount || strtolower($fromAccount->nid) !== strtolower($request->nid)) {
            return redirect('/dashboard')->with('transfer_error', '❌ Source account validation failed. Check Account Number or NID.');
        }
        $toAccount = $this->findActiveAccount($request->to_account);
        if (!$toAccount) {
            return redirect('/dashboard')->with('transfer_error', '❌ Destination account not found or inactive.');
        }
        if ($fromAccount->account_number === $toAccount->account_number) {
            return redirect('/dashboard')->with('transfer_error', '❌ Cannot transfer to the same account.');
        }
        return redirect('/dashboard')->with('transfer_accounts', [
            'from_account' => $fromAccount->account_number, 'from_name'    => $fromAccount->full_name,
            'from_balance' => $fromAccount->balance,        'from_user_id' => $fromAccount->user_id,
            'to_account'   => $toAccount->account_number,  'to_name'      => $toAccount->full_name,
            'to_balance'   => $toAccount->balance,          'to_user_id'   => $toAccount->user_id,
        ]);
    }

    public function transferGenerateOtp(Request $request)
    {
        $request->validate(['from_account' => 'required|string', 'to_account' => 'required|string', 'amount' => 'required|numeric|min:0.01']);
        $fromAccount = $this->findActiveAccount($request->from_account);
        $toAccount   = $this->findActiveAccount($request->to_account);
        if (!$fromAccount || $fromAccount->balance < $request->amount) {
            return redirect('/dashboard')->with('transfer_error', '❌ Insufficient balance in source account.');
        }
        if (!$toAccount) {
            return redirect('/dashboard')->with('transfer_error', '❌ Destination account not found.');
        }
        $otp   = $this->generateOtp();
        $otpId = DB::table('otp_verification')->insertGetId([
            'account_number'   => $request->from_account,
            'receiver_account' => $request->to_account,
            'amount'           => $request->amount,
            'otp'              => $otp,
            'type'             => 'TRANSFER',
            'expires_at'       => Carbon::now()->addMinutes(5),
            'created_at'       => now(), 'updated_at' => now(),
        ]);
        DB::table('notifications')->insert([
            'user_id'    => $fromAccount->user_id,
            'message'    => "🔐 Transfer Verification OTP: {$otp}\nAmount: $" . number_format($request->amount, 2) . " → {$request->to_account}\nValid for 5 minutes.",
            'is_read'    => 0, 'created_at' => now(), 'updated_at' => now(),
        ]);
        return redirect('/dashboard')
            ->with('transfer_otp_id', $otpId)
            ->with('transfer_accounts', [
                'from_account' => $request->from_account,  'from_name'    => $fromAccount->full_name,
                'from_balance' => $fromAccount->balance,   'from_user_id' => $fromAccount->user_id,
                'to_account'   => $request->to_account,    'to_name'      => $toAccount->full_name,
                'to_balance'   => $toAccount->balance,      'to_user_id'   => $toAccount->user_id,
            ]);
    }

    public function transferVerifyOtp(Request $request)
    {
        $request->validate(['otp_id' => 'required|integer', 'otp' => 'required|string|size:6']);
        $record = DB::table('otp_verification')->where('id', $request->otp_id)->first();
        if (!$record) {
            return redirect('/dashboard')->with('transfer_error', '❌ OTP session not found. Please start again.');
        }
        if (Carbon::parse($record->expires_at)->isPast()) {
            DB::table('otp_verification')->where('id', $record->id)->delete();
            return redirect('/dashboard')->with('transfer_error', '❌ OTP Expired. Please generate a new one.');
        }
        if ($record->otp !== $request->otp) {
            return redirect('/dashboard')
                ->with('transfer_error', '❌ Invalid OTP. Please try again.')
                ->with('transfer_otp_id', $record->id);
        }
        DB::transaction(function () use ($record) {
            DB::table('accounts')->where('account_number', $record->account_number)->decrement('balance', $record->amount);
            DB::table('accounts')->where('account_number', $record->receiver_account)->increment('balance', $record->amount);
            $fromAcc = DB::table('accounts')
                ->join('USERS', 'accounts.user_id', '=', 'USERS.id')
                ->select('accounts.id as acc_id', 'accounts.balance', 'USERS.id as user_id')
                ->where('accounts.account_number', $record->account_number)->first();
            $toAcc = DB::table('accounts')
                ->join('USERS', 'accounts.user_id', '=', 'USERS.id')
                ->select('accounts.id as acc_id', 'accounts.balance', 'USERS.id as user_id')
                ->where('accounts.account_number', $record->receiver_account)->first();
            DB::table('transactions')->insert([
                'account_id' => $fromAcc->acc_id, 'transaction_type' => 'TRANSFER_OUT',
                'amount' => $record->amount, 'reference' => $record->receiver_account,
                'description' => "Transfer to account {$record->receiver_account}",
                'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('transactions')->insert([
                'account_id' => $toAcc->acc_id, 'transaction_type' => 'TRANSFER_IN',
                'amount' => $record->amount, 'reference' => $record->account_number,
                'description' => "Transfer received from account {$record->account_number}",
                'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('notifications')->insert([
                'user_id' => $fromAcc->user_id,
                'message' => "✅ Transfer Successful!\nSent: $" . number_format($record->amount, 2) . " → {$record->receiver_account}\nNew Balance: $" . number_format($fromAcc->balance, 2),
                'is_read' => 0, 'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('notifications')->insert([
                'user_id' => $toAcc->user_id,
                'message' => "💰 Money Received!\nAmount: $" . number_format($record->amount, 2) . " from {$record->account_number}\nNew Balance: $" . number_format($toAcc->balance, 2),
                'is_read' => 0, 'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('audit_log')->insert([
                'table_name' => 'accounts', 'action' => 'TRANSFER',
                'performed_by' => auth()->user()->full_name ?? 'Employee',
                'details' => "Transferred $" . number_format($record->amount, 2) . " from {$record->account_number} to {$record->receiver_account}",
                'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('otp_verification')->where('id', $record->id)->delete();
        });
        return redirect('/dashboard')->with('transfer_success', '✅ Transfer of $' . number_format($record->amount, 2) . ' completed successfully!');
    }
}
