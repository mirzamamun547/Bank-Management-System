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
            
        $pendingLoans = DB::table('PENDING_LOANS')->get();
        
        // Enhance pending loans with eligibility check
        $pdo = DB::getPdo();
        foreach ($pendingLoans as $loan) {
            $stmt = $pdo->prepare("BEGIN :result := CHECK_LOAN_ELIGIBILITY(:user_id); END;");
            $stmt->bindParam(':user_id', $loan->user_id);
            $stmt->bindParam(':result', $eligibility, \PDO::PARAM_STR, 255);
            $stmt->execute();
            $loan->eligibility = $eligibility;
        }

        return view('admin.dashboard', compact('pendingAccounts', 'activeAccounts', 'customers', 'pendingLoans'));
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

        $pin           = mt_rand(1000, 9999);
        $performedBy   = auth()->user()->full_name ?? 'Employee';

        try {
            $pdo  = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN APPROVE_ACCOUNT(:account_id, :pin, :performed_by); END;");
            $stmt->bindParam(':account_id',   $id);
            $stmt->bindParam(':pin',           $pin);
            $stmt->bindParam(':performed_by',  $performedBy);
            $stmt->execute();

        } catch (\Exception $e) {
            return back()->with('error', 'Approval failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Account approved successfully. Notification sent to customer.');
    }

    public function approveLoan(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:APPROVE,REJECT'
        ]);

        $loanId      = (int) $id;
        $action      = $request->input('action');
        $performedBy = auth()->user()->full_name ?? 'Employee';

        try {
            $pdo  = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN APPROVE_LOAN(:loan_id, :action, :performed_by); END;");
            $stmt->bindParam(':loan_id', $loanId);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':performed_by', $performedBy);
            $stmt->execute();
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            if (preg_match('/ORA-\d+: (.*)/', $errorMsg, $matches)) {
                $errorMsg = $matches[1];
            }
            return back()->with('error', 'Loan action failed: ' . $errorMsg);
        }

        $statusStr = $action === 'APPROVE' ? 'approved' : 'rejected';
        return back()->with('success', 'Loan ' . $statusStr . ' successfully.');
    }

   

    // Oracle FIND_ACTIVE_ACCOUNT Function Call
    private function findActiveAccount(string $accountNumber)
    {
        $pdo  = DB::getPdo();
        $stmt = $pdo->prepare("
            DECLARE
                v_cursor SYS_REFCURSOR;
            BEGIN
                v_cursor := FIND_ACTIVE_ACCOUNT(:account_number);
                :result  := v_cursor;
            END;
        ");

        $stmt->bindParam(':account_number', $accountNumber);
        $stmt->bindParam(':result', $cursor, \PDO::PARAM_STMT);
        $stmt->execute();

        oci_execute($cursor, OCI_DEFAULT);

        $rows = [];
        while ($row = oci_fetch_assoc($cursor)) {
            $rows[] = (object) array_change_key_case($row, CASE_LOWER);
        }

        oci_free_statement($cursor);

        return $rows[0] ?? null;
    }

    private function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function depositSearch(Request $request)
    {
        $request->validate(['account_number' => 'required|string', 'nid' => 'required|string']);
        $account = $this->findActiveAccount($request->account_number);
        if (!$account || strtolower($account->nid) !== strtolower($request->nid)) {
            return redirect('/dashboard')->with('deposit_error', 'Invalid Account Number or NID. Please try again.');
        }
        return redirect('/dashboard')->with('deposit_customer', [
            'account_id'     => $account->account_id,
            'account_number' => $account->account_number,
            'full_name'      => $account->full_name,
            'balance'        => $account->balance,
            'user_id'        => $account->user_id,
        ]);
    }

    public function depositGenerateOtp(Request $request)
    {
        $request->validate(['account_number' => 'required|string', 'amount' => 'required|numeric|min:0.01']);
        $account = $this->findActiveAccount($request->account_number);
        if (!$account) {
            return redirect('/dashboard')->with('deposit_error', ' Account not found.');
        }

        $otp   = $this->generateOtp();
        $otpId = DB::table('otp_verification')->insertGetId([
            'account_number' => $request->account_number,
            'amount'         => $request->amount,
            'otp'            => $otp,
            'type'           => 'DEPOSIT',
            'expires_at'     => Carbon::now()->addMinutes(5),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        DB::table('notifications')->insert([
            'user_id'    => $account->user_id,
            'message'    => " Deposit Verification OTP: {$otp}\nAmount: $" . number_format($request->amount, 2) . "\nValid for 5 minutes.",
            'is_read'    => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/dashboard')
            ->with('deposit_otp_id', $otpId)
            ->with('deposit_customer', [
                'account_id'     => $account->account_id,
                'account_number' => $account->account_number,
                'full_name'      => $account->full_name,
                'balance'        => $account->balance,
                'user_id'        => $account->user_id,
            ]);
    }

    // Oracle DO_DEPOSIT Procedure Call
    public function depositVerifyOtp(Request $request)
{
    $request->validate(['otp_id' => 'required|integer', 'otp' => 'required|string|size:6']);
    $record = DB::table('otp_verification')->where('id', $request->otp_id)->first();

    if (!$record) {
        return redirect('/dashboard')->with('deposit_error', ' OTP session not found. Please start again.');
    }
    if (Carbon::parse($record->expires_at)->isPast()) {
        DB::table('otp_verification')->where('id', $record->id)->delete();
        return redirect('/dashboard')->with('deposit_error', 'OTP Expired. Please generate a new one.');
    }
    if ($record->otp !== $request->otp) {
        return redirect('/dashboard')
            ->with('deposit_error', ' Invalid OTP. Please try again.')
            ->with('deposit_otp_id', $record->id);
    }


    \Illuminate\Support\Facades\Log::info('=== DEPOSIT DEBUG ===');
    \Illuminate\Support\Facades\Log::info('Record keys: ' . implode(', ', array_keys((array)$record)));
    \Illuminate\Support\Facades\Log::info('account_number: ' . ($record->account_number ?? 'NULL'));
    \Illuminate\Support\Facades\Log::info('amount: ' . ($record->amount ?? 'NULL'));
    \Illuminate\Support\Facades\Log::info('Full record: ' . json_encode((array)$record));
    

    try {
        $performedBy = auth()->user()->full_name ?? 'Employee';
        $pdo         = DB::getPdo();
        $stmt        = $pdo->prepare("BEGIN DO_DEPOSIT(:account_number, :amount, :performed_by); END;");
        $stmt->bindParam(':account_number', $record->account_number);
        $stmt->bindParam(':amount',         $record->amount);
        $stmt->bindParam(':performed_by',   $performedBy);
        $stmt->execute();

        \Illuminate\Support\Facades\Log::info('DO_DEPOSIT executed successfully');

        DB::table('otp_verification')->where('id', $record->id)->delete();

    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('DO_DEPOSIT Error: ' . $e->getMessage());
        return redirect('/dashboard')->with('deposit_error', ' Deposit failed: ' . $e->getMessage());
    }

    return redirect('/dashboard')->with('deposit_success', 'Deposit of $' . number_format($record->amount, 2) . ' completed successfully!');
}
    
   
    public function withdrawSearch(Request $request)
    {
        $request->validate(['account_number' => 'required|string', 'nid' => 'required|string']);
        $account = $this->findActiveAccount($request->account_number);
        if (!$account || strtolower($account->nid) !== strtolower($request->nid)) {
            return redirect('/dashboard')->with('withdraw_error', ' Invalid Account Number or NID. Please try again.');
        }
        return redirect('/dashboard')->with('withdraw_customer', [
            'account_id'     => $account->account_id,
            'account_number' => $account->account_number,
            'full_name'      => $account->full_name,
            'balance'        => $account->balance,
            'user_id'        => $account->user_id,
        ]);
    }

    public function withdrawGenerateOtp(Request $request)
    {
        $request->validate(['account_number' => 'required|string', 'amount' => 'required|numeric|min:0.01']);
        $account = $this->findActiveAccount($request->account_number);
        if (!$account) {
            return redirect('/dashboard')->with('withdraw_error', ' Account not found.');
        }
        if ($account->balance < $request->amount) {
            return redirect('/dashboard')
                ->with('withdraw_error', ' Insufficient balance. Available: $' . number_format($account->balance, 2))
                ->with('withdraw_customer', [
                    'account_id'     => $account->account_id,
                    'account_number' => $account->account_number,
                    'full_name'      => $account->full_name,
                    'balance'        => $account->balance,
                    'user_id'        => $account->user_id,
                ]);
        }

        $otp   = $this->generateOtp();
        $otpId = DB::table('otp_verification')->insertGetId([
            'account_number' => $request->account_number,
            'amount'         => $request->amount,
            'otp'            => $otp,
            'type'           => 'WITHDRAW',
            'expires_at'     => Carbon::now()->addMinutes(5),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        DB::table('notifications')->insert([
            'user_id'    => $account->user_id,
            'message'    => "Withdrawal Verification OTP: {$otp}\nAmount: $" . number_format($request->amount, 2) . "\nValid for 5 minutes.",
            'is_read'    => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/dashboard')
            ->with('withdraw_otp_id', $otpId)
            ->with('withdraw_customer', [
                'account_id'     => $account->account_id,
                'account_number' => $account->account_number,
                'full_name'      => $account->full_name,
                'balance'        => $account->balance,
                'user_id'        => $account->user_id,
            ]);
    }

    // Oracle DO_WITHDRAW Procedure Call
    
public function withdrawVerifyOtp(Request $request)
{
    $request->validate(['otp_id' => 'required|integer', 'otp' => 'required|string|size:6']);
    $record = DB::table('otp_verification')->where('id', $request->otp_id)->first();

    if (!$record) {
        return redirect('/dashboard')->with('withdraw_error', 'OTP session not found. Please start again.');
    }
    if (Carbon::parse($record->expires_at)->isPast()) {
        DB::table('otp_verification')->where('id', $record->id)->delete();
        return redirect('/dashboard')->with('withdraw_error', 'OTP Expired. Please generate a new one.');
    }
    if ($record->otp !== $request->otp) {
        return redirect('/dashboard')
            ->with('withdraw_error', 'Invalid OTP. Please try again.')
            ->with('withdraw_otp_id', $record->id);
    }

    
    \Illuminate\Support\Facades\Log::info('=== WITHDRAW DEBUG ===');
    \Illuminate\Support\Facades\Log::info('Record keys: ' . implode(', ', array_keys((array)$record)));
    \Illuminate\Support\Facades\Log::info('account_number: ' . ($record->account_number ?? 'NULL'));
    \Illuminate\Support\Facades\Log::info('amount: ' . ($record->amount ?? 'NULL'));
    \Illuminate\Support\Facades\Log::info('Full record: ' . json_encode((array)$record));
   

    try {
        $performedBy = auth()->user()->full_name ?? 'Employee';
        $pdo         = DB::getPdo();
        $stmt        = $pdo->prepare("BEGIN DO_WITHDRAW(:account_number, :amount, :performed_by); END;");
        $stmt->bindParam(':account_number', $record->account_number);
        $stmt->bindParam(':amount',         $record->amount);
        $stmt->bindParam(':performed_by',   $performedBy);
        $stmt->execute();

        \Illuminate\Support\Facades\Log::info('DO_WITHDRAW executed successfully');

        DB::table('otp_verification')->where('id', $record->id)->delete();

    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('DO_WITHDRAW Error: ' . $e->getMessage());
        return redirect('/dashboard')->with('withdraw_error', 'Withdrawal failed: ' . $e->getMessage());
    }

    return redirect('/dashboard')->with('withdraw_success', 'Withdrawal of $' . number_format($record->amount, 2) . ' completed successfully!');
}
    
    public function transferSearch(Request $request)
    {
        $request->validate(['from_account' => 'required|string', 'to_account' => 'required|string', 'nid' => 'required|string']);
        $fromAccount = $this->findActiveAccount($request->from_account);
        if (!$fromAccount || strtolower($fromAccount->nid) !== strtolower($request->nid)) {
            return redirect('/dashboard')->with('transfer_error', ' Source account validation failed. Check Account Number or NID.');
        }
        $toAccount = $this->findActiveAccount($request->to_account);
        if (!$toAccount) {
            return redirect('/dashboard')->with('transfer_error', 'Destination account not found or inactive.');
        }
        if ($fromAccount->account_number === $toAccount->account_number) {
            return redirect('/dashboard')->with('transfer_error', ' Cannot transfer to the same account.');
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
            return redirect('/dashboard')->with('transfer_error', ' Insufficient balance in source account.');
        }
        if (!$toAccount) {
            return redirect('/dashboard')->with('transfer_error', ' Destination account not found.');
        }

        $otp   = $this->generateOtp();
        $otpId = DB::table('otp_verification')->insertGetId([
            'account_number'   => $request->from_account,
            'receiver_account' => $request->to_account,
            'amount'           => $request->amount,
            'otp'              => $otp,
            'type'             => 'TRANSFER',
            'expires_at'       => Carbon::now()->addMinutes(5),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        DB::table('notifications')->insert([
            'user_id'    => $fromAccount->user_id,
            'message'    => "Transfer Verification OTP: {$otp}\nAmount: $" . number_format($request->amount, 2) . " → {$request->to_account}\nValid for 5 minutes.",
            'is_read'    => 0,
            'created_at' => now(),
            'updated_at' => now(),
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

    // Oracle DO_TRANSFER Procedure Call
    public function transferVerifyOtp(Request $request)
    {
        $request->validate(['otp_id' => 'required|integer', 'otp' => 'required|string|size:6']);
        $record = DB::table('otp_verification')->where('id', $request->otp_id)->first();

        if (!$record) {
            return redirect('/dashboard')->with('transfer_error', ' OTP session not found. Please start again.');
        }
        if (Carbon::parse($record->expires_at)->isPast()) {
            DB::table('otp_verification')->where('id', $record->id)->delete();
            return redirect('/dashboard')->with('transfer_error', ' OTP Expired. Please generate a new one.');
        }
        if ($record->otp !== $request->otp) {
            return redirect('/dashboard')
                ->with('transfer_error', ' Invalid OTP. Please try again.')
                ->with('transfer_otp_id', $record->id);
        }

        try {
            // Oracle DO_TRANSFER Procedure Call
            $performedBy = auth()->user()->full_name ?? 'Employee';
            $pdo         = DB::getPdo();
            $stmt        = $pdo->prepare("BEGIN DO_TRANSFER(:from_account, :to_account, :amount, :performed_by); END;");
            $stmt->bindParam(':from_account',  $record->account_number);
            $stmt->bindParam(':to_account',    $record->receiver_account);
            $stmt->bindParam(':amount',        $record->amount);
            $stmt->bindParam(':performed_by',  $performedBy);
            $stmt->execute();

            // OTP delete করো
            DB::table('otp_verification')->where('id', $record->id)->delete();

        } catch (\Exception $e) {
            return redirect('/dashboard')->with('transfer_error', ' Transfer failed: ' . $e->getMessage());
        }

        return redirect('/dashboard')->with('transfer_success', ' Transfer of $' . number_format($record->amount, 2) . ' completed successfully!');
    }
}