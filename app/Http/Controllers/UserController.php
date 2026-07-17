<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function showProfile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

     
        
        $user->first_name = trim($request->first_name);
        $user->last_name = trim($request->last_name);
        $user->full_name = trim($request->first_name) . ' ' . trim($request->last_name);
        $user->phone = trim($request->phone);
        $user->address = trim($request->address);
        
        $user->save();

        return back()->with('profile_success', 'Profile updated successfully!');
    }

    // Update password
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed', 
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['password_error' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('password_success', 'Password updated successfully!');
    }

    public function indexAccounts(Request $request)
    {
        $user = Auth::user();
        $query = $user->accounts();

        if ($request->has('type') && $request->type !== 'All Accounts') {
            $query->where('account_type', $request->type);
        }

        $accounts = $query->get();
        $branches = \Illuminate\Support\Facades\DB::table('branches')->where('status', 'ACTIVE')->get();
        $settings = \Illuminate\Support\Facades\DB::table('system_settings')->pluck('settings_value', 'settings_key')->toArray();

        return view('user.accounts', compact('accounts', 'branches', 'settings'));
    }

    public function storeAccount(Request $request)
    {
        $request->validate([
            'account_type' => 'required|string|in:Saving,Current,FD',
            'opening_balance' => 'required|numeric|min:0',
            'branch_id' => 'required|integer',
            'father_name' => 'nullable|string|max:100',
            'mother_name' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:Male,Female,Transgender',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:USERS,EMAIL,' . Auth::id(),
            'address' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'profile_photo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        
        
        if ($request->filled('father_name')) $user->FATHER_NAME = $request->father_name;
        if ($request->filled('mother_name')) $user->MOTHER_NAME = $request->mother_name;
        if ($request->filled('gender')) $user->GENDER = $request->gender;
        if ($request->filled('mobile')) $user->PHONE = $request->mobile;
        if ($request->filled('email')) $user->EMAIL = $request->email;
        if ($request->filled('address')) $user->ADDRESS = $request->address;
        if ($request->filled('dob')) $user->DOB = $request->dob;

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('uploads/profiles', 'public');
            $user->PROFILE_PHOTO = $path;
        }

        if ($request->hasFile('signature')) {
            $path = $request->file('signature')->store('uploads/signatures', 'public');
            $user->SIGNATURE = $path;
        }

        $user->save();
        
      
        $accountTypeMap = [
            'Saving' => 'Savings Account',
            'Current' => 'Current Account',
            'FD' => 'Fixed Deposit',
        ];
        $mappedType = $accountTypeMap[$request->account_type] ?? 'Savings Account';

        // Generate a random unique account number
        $account_number = 'ACC-' . random_int(10000, 99999);
        while (\App\Models\Account::where('account_number', $account_number)->exists()) {
            $account_number = 'ACC-' . random_int(10000, 99999);
        }

        $branchObj = \Illuminate\Support\Facades\DB::table('branches')->where('branch_id', $request->branch_id)->first();
        $branchName = $branchObj ? $branchObj->branch_name : 'Central Branch';

        $user->accounts()->create([
            'account_number' => $account_number,
            'account_type' => $mappedType,
            'balance' => $request->opening_balance,
            'status' => 'Pending',
            'branch' => $branchName,
            'branch_id' => $request->branch_id,
        ]);

        return back()->with('success', 'Account application submitted successfully! It is pending approval.');
    }

    public function notifications()
    {
        $user = Auth::user();
        $notifications = \Illuminate\Support\Facades\DB::table('notifications')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Mark as read optionally
        \Illuminate\Support\Facades\DB::table('notifications')
            ->where('user_id', $user->id)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return view('user.notifications', compact('notifications'));
    }

    public function dashboard()
    {
        $user = Auth::user();
        $accounts = $user->accounts()->where('status', 'Active')->get();
        
        $totalBalance = $accounts->sum('balance');
        $recentTransactions = collect(); // We will fetch from all accounts
        
        if ($accounts->isNotEmpty()) {
            $accountIds = $accounts->pluck('id')->toArray();
            $recentTransactions = \Illuminate\Support\Facades\DB::table('transactions')
                ->whereIn('account_id', $accountIds)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        $notifications = \Illuminate\Support\Facades\DB::table('notifications')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('user.dashboard', compact('user', 'accounts', 'totalBalance', 'recentTransactions', 'notifications'));
    }

    public function indexTransfer()
    {
        $user = Auth::user();
        $accounts = $user->accounts()->where('status', 'Active')->get();
        return view('user.transfer', compact('accounts'));
    }

    public function storeTransfer(Request $request)
    {
        $request->validate([
            'from_account' => 'required|string',
            'to_account' => 'required|string',
            'amount' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        
        // Verify from account belongs to user
        $fromAccount = $user->accounts()->where('account_number', $request->from_account)->where('status', 'Active')->first();
        if (!$fromAccount) {
            return back()->with('error', 'Invalid source account.');
        }

        try {
            $pdo = \Illuminate\Support\Facades\DB::getPdo();
            $stmt = $pdo->prepare("BEGIN DO_TRANSFER(:from_acc, :to_acc, :amount, :performed_by); END;");
            $stmt->execute([
                'from_acc' => $request->from_account,
                'to_acc' => $request->to_account,
                'amount' => $request->amount,
                'performed_by' => $user->full_name,
            ]);
            
            return back()->with('success', 'Transfer completed successfully!');
        } catch (\Exception $e) {
            // Extract ORA error message safely
            $errorMsg = $e->getMessage();
            if (preg_match('/ORA-\d+: (.*)/', $errorMsg, $matches)) {
                $errorMsg = $matches[1];
            }
            return back()->with('error', 'Transfer failed: ' . $errorMsg);
        }
    }

    public function indexTransactions(Request $request)
    {
        $user = Auth::user();
        $userAccounts = $user->accounts()->get();
        $accountIds   = $userAccounts->pluck('id')->toArray();

        $query = \Illuminate\Support\Facades\DB::table('transactions')
            ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select(
                'transactions.id',
                'transactions.transaction_type',
                'transactions.amount',
                'transactions.description',
                'transactions.created_at',
                'accounts.account_number',
                'accounts.branch'
            )
            ->whereIn('transactions.account_id', $accountIds)
            ->orderBy('transactions.created_at', 'desc');

        // Filter by transaction type
        if ($request->filled('type') && $request->type !== 'All') {
            $query->where('transactions.transaction_type', strtoupper($request->type));
        }

        // Filter by from-date
        if ($request->filled('from_date')) {
            $query->whereDate('transactions.created_at', '>=', $request->from_date);
        }

        // Filter by to-date
        if ($request->filled('to_date')) {
            $query->whereDate('transactions.created_at', '<=', $request->to_date);
        }

        // Filter by specific account
        if ($request->filled('account') && in_array((int) $request->account, $accountIds)) {
            $query->where('transactions.account_id', (int) $request->account);
        }

        $transactions = $query->paginate(15)->withQueryString();
        $totalCredit  = \Illuminate\Support\Facades\DB::table('transactions')
            ->whereIn('account_id', $accountIds)
            ->whereIn('transaction_type', ['DEPOSIT', 'TRANSFER_IN'])
            ->sum('amount');
        $totalDebit   = \Illuminate\Support\Facades\DB::table('transactions')
            ->whereIn('account_id', $accountIds)
            ->whereIn('transaction_type', ['WITHDRAWAL', 'TRANSFER_OUT', 'LOAN_EMI'])
            ->sum('amount');

        return view('user.transactions', compact('transactions', 'userAccounts', 'totalCredit', 'totalDebit'));
    }

    public function indexLoans()
    {
        $user = Auth::user();
        $loans = $user->loans()->orderBy('created_at', 'desc')->get();
        $accounts = $user->accounts()->where('status', 'Active')->get();
        $settings = \Illuminate\Support\Facades\DB::table('system_settings')->pluck('settings_value', 'settings_key')->toArray();
        
        return view('user.loan', compact('loans', 'accounts', 'settings'));
    }

    public function applyLoan(Request $request)
    {
        $request->validate([
            'loan_type' => 'required|string',
            'amount' => 'required|numeric|min:500',
            'duration_months' => 'required|integer|min:6',
            'purpose' => 'nullable|string'
        ]);

        $user = Auth::user();

        try {
            $pdo = \Illuminate\Support\Facades\DB::getPdo();
            $stmt = $pdo->prepare("BEGIN APPLY_LOAN(:user_id, :loan_type, :amount, :duration_months, :purpose); END;");
            $stmt->execute([
                'user_id' => $user->id,
                'loan_type' => $request->loan_type,
                'amount' => $request->amount,
                'duration_months' => $request->duration_months,
                'purpose' => $request->purpose
            ]);

            // Insert notification for all employees
            $employeeIds = DB::table('users')->where('role', 'EMPLOYEE')->pluck('id')->toArray();
            $now = now();
            $rows = [];
            foreach ($employeeIds as $empId) {
                $rows[] = [
                    'user_id'    => $empId,
                    'message'    => "Customer {$user->full_name} submitted a new loan request (₹" . number_format($request->amount, 2) . ").",
                    'is_read'    => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            if (!empty($rows)) {
                DB::table('notifications')->insert($rows);
            }
            return back()->with('success', 'Loan application submitted successfully.');
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            if (preg_match('/ORA-\d+: (.*)/', $errorMsg, $matches)) {
                $errorMsg = $matches[1];
            }
            return back()->with('error', 'Loan application failed: ' . $errorMsg);
        }
    }

    public function payLoanEmi(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'account_id' => 'required|exists:accounts,id'
        ]);

        $user = Auth::user();
        
        // Ensure account belongs to user and is active
        $account = $user->accounts()->where('id', $request->account_id)->where('status', 'Active')->first();
        if (!$account) {
            return back()->with('error', 'Invalid or inactive account selected.');
        }

        // Fetch loan
        $loan = $user->loans()->where('id', $request->loan_id)->first();
        if (!$loan || $loan->status !== 'Active') {
            return back()->with('error', 'Invalid loan or loan is not active.');
        }

        try {
            $pdo = \Illuminate\Support\Facades\DB::getPdo();
            $stmt = $pdo->prepare("BEGIN PAY_LOAN_EMI(:loan_id, :account_id, :emi_amount); END;");
            $stmt->execute([
                'loan_id' => $loan->id,
                'account_id' => $account->id,
                'emi_amount' => $loan->monthly_installment
            ]);

            return back()->with('success', 'EMI payment processed successfully.');
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            if (preg_match('/ORA-\d+: (.*)/', $errorMsg, $matches)) {
                $errorMsg = $matches[1];
            }
            return back()->with('error', 'EMI payment failed: ' . $errorMsg);
        }
    }
}
