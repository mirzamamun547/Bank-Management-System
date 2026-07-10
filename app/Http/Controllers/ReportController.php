<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display various system reports based on Oracle views and aggregates.
     */
    public function index(Request $request)
    {
        // 1. Customer Report
        $customerReport = DB::table('USERS')
            ->where('ROLE', 'CUSTOMER')
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Employee Report
        $employeeReport = DB::table('USERS')
            ->leftJoin('branches', 'USERS.branch_id', '=', 'branches.branch_id')
            ->where('USERS.ROLE', 'EMPLOYEE')
            ->select('USERS.*', 'branches.branch_name')
            ->orderBy('USERS.created_at', 'desc')
            ->get();

        // 3. Branch Report (using BRANCH_SUMMARY_VIEW)
        $branchReport = DB::table('BRANCH_SUMMARY_VIEW')->get();

        // 4. Account Report
        $accountReport = DB::table('accounts')
            ->join('USERS', 'accounts.user_id', '=', 'USERS.id')
            ->leftJoin('branches', 'accounts.branch_id', '=', 'branches.branch_id')
            ->select('accounts.*', 'USERS.first_name', 'USERS.last_name', 'USERS.customer_id', 'branches.branch_name')
            ->orderBy('accounts.created_at', 'desc')
            ->get();

        // 5. Loan Report
        $loanReport = DB::table('loans')
            ->join('USERS', 'loans.user_id', '=', 'USERS.id')
            ->select('loans.*', 'USERS.first_name', 'USERS.last_name', 'USERS.customer_id')
            ->orderBy('loans.created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'customerReport',
            'employeeReport',
            'branchReport',
            'accountReport',
            'loanReport'
        ))->with('section', 'reports');
    }
}
