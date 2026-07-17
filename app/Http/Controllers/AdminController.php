<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display the Admin Dashboard with stats from ADMIN_DASHBOARD_VIEW.
     */
    public function dashboard()
    {
        $stats = DB::table('ADMIN_DASHBOARD_VIEW')->first();

        // Also fetch monthly transaction summary for the chart
        $monthlyTransactions = DB::table('MONTHLY_TRANSACTION_VIEW')
            ->orderBy('month', 'asc')
            ->get();

        return view('admin.dashboard', compact('stats', 'monthlyTransactions'));
    }

    /**
     * Customer Management
     */
    public function customers(Request $request)
    {
        $search = $request->input('search');
        
        $query = DB::table('USERS')
            ->where('ROLE', 'CUSTOMER');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('FIRST_NAME', 'like', "%{$search}%")
                  ->orWhere('LAST_NAME', 'like', "%{$search}%")
                  ->orWhere('EMAIL', 'like', "%{$search}%")
                  ->orWhere('CUSTOMER_ID', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.dashboard', compact('customers'))->with('section', 'customers');
    }

    public function suspendCustomer(Request $request, $id)
    {
        $status = $request->input('status', 'SUSPENDED');
        
        DB::table('USERS')
            ->where('id', $id)
            ->where('role', 'CUSTOMER')
            ->update([
                'status' => $status,
                'updated_at' => now()
            ]);

        return redirect()->back()->with('success', 'Customer status updated successfully.');
    }

    /**
     * Employee Management
     */
    public function employees()
    {
        $employees = DB::table('USERS')
            ->where('ROLE', 'EMPLOYEE')
            ->orderBy('created_at', 'desc')
            ->get();

        $branches = DB::table('branches')
            ->where('status', 'ACTIVE')
            ->get();

        return view('admin.dashboard', compact('employees', 'branches'))->with('section', 'employees');
    }

    public function storeEmployee(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:USERS,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'nid' => 'required|string|max:50',
            'dob' => 'required|date',
            'password' => 'required|string|min:6',
            'branch_id' => 'required|numeric'
        ]);

        try {
            // Call Oracle PL/SQL package procedure to add employee
            DB::statement("
                BEGIN
                    ADD_EMPLOYEE(
                        :first_name,
                        :last_name,
                        :email,
                        :phone,
                        :address,
                        :nid,
                        TO_DATE(:dob, 'YYYY-MM-DD'),
                        :password,
                        :branch_id
                    );
                END;
            ", [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'nid' => $request->nid,
                'dob' => Carbon::parse($request->dob)->format('Y-m-d'),
                'password' => Hash::make($request->password),
                'branch_id' => $request->branch_id
            ]);

            return redirect()->back()->with('success', 'Employee created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Oracle PL/SQL Error: ' . $e->getMessage());
        }
    }

    public function toggleEmployeeStatus(Request $request, $id)
    {
        $employee = DB::table('USERS')->where('id', $id)->where('role', 'EMPLOYEE')->first();
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        $newStatus = ($employee->status === 'ACTIVE') ? 'INACTIVE' : 'ACTIVE';

        try {
            DB::statement("
                BEGIN
                    UPDATE_EMPLOYEE(
                        :emp_id,
                        :first_name,
                        :last_name,
                        :email,
                        :phone,
                        :address,
                        :status
                    );
                END;
            ", [
                'emp_id' => $id,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'address' => $employee->address,
                'status' => $newStatus
            ]);

            return redirect()->back()->with('success', 'Employee status updated successfully to ' . $newStatus);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Oracle PL/SQL Error: ' . $e->getMessage());
        }
    }

    /**
     * Account Monitoring
     */
    public function accounts(Request $request)
    {
        $search = $request->input('search');
        $branchId = $request->input('branch_id');
        $type = $request->input('type');
        $status = $request->input('status');

        $query = Account::with(['user', 'branchInfo']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('account_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('customer_id', 'like', "%{$search}%");
                  });
            });
        }

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($type) {
            $query->where('account_type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $accounts = $query->orderBy('created_at', 'desc')->paginate(10);
        $branches = DB::table('branches')->get();

        return view('admin.dashboard', compact('accounts', 'branches'))->with('section', 'accounts');
    }

    /**
     * Loan Approval/Rejection
     */
    public function loans()
    {
        $loans = DB::table('loans')
            ->join('USERS', 'loans.user_id', '=', 'USERS.id')
            ->select('loans.*', 'USERS.first_name', 'USERS.last_name', 'USERS.customer_id')
            ->orderBy('loans.created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact('loans'))->with('section', 'loans');
    }

    public function approveLoan($id)
    {
        try {
            DB::statement("
                BEGIN
                    APPROVE_LOAN(:loan_id);
                END;
            ", ['loan_id' => $id]);

            return redirect()->back()->with('success', 'Loan approved and funds disbursed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Oracle PL/SQL Error: ' . $e->getMessage());
        }
    }

    public function rejectLoan($id)
    {
        try {
            DB::statement("
                BEGIN
                    REJECT_LOAN(:loan_id);
                END;
            ", ['loan_id' => $id]);

            return redirect()->back()->with('success', 'Loan request rejected.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Oracle PL/SQL Error: ' . $e->getMessage());
        }
    }

    /**
     * Audit Logs
     */
    public function auditLogs()
    {
        $logs = DB::table('audit_log')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.dashboard', compact('logs'))->with('section', 'audit-logs');
    }

    /**
     * System Settings
     */
    public function settings()
    {
        $settings = DB::table('system_settings')->pluck('settings_value', 'settings_key')->toArray();
        return view('admin.dashboard', compact('settings'))->with('section', 'settings');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'interest_rate' => 'required|numeric',
            'loan_interest' => 'required|numeric',
            'otp_expiry' => 'required|numeric',
            'minimum_balance' => 'required|numeric',
        ]);

        DB::table('system_settings')->where('settings_key', 'INTEREST_RATE')->update(['settings_value' => $request->interest_rate, 'updated_at' => now()]);
        DB::table('system_settings')->where('settings_key', 'LOAN_INTEREST')->update(['settings_value' => $request->loan_interest, 'updated_at' => now()]);
        DB::table('system_settings')->where('settings_key', 'OTP_EXPIRY')->update(['settings_value' => $request->otp_expiry, 'updated_at' => now()]);
        DB::table('system_settings')->where('settings_key', 'MINIMUM_BALANCE')->update(['settings_value' => $request->minimum_balance, 'updated_at' => now()]);

        return redirect()->back()->with('success', 'System settings updated successfully.');
    }
}
