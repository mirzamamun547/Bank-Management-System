<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Account;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Aggregate statistics for the new admin panel
        $stats = [
            'total_customers' => DB::table('USERS')->where('role', 'CUSTOMER')->count(),
            'total_employees' => DB::table('USERS')->where('role', 'EMPLOYEE')->count(),
            'total_accounts' => DB::table('accounts')->count(),
            'total_balance' => DB::table('accounts')->sum('balance'),
            'total_loans' => DB::table('loans')->count(),
            'total_branches' => 24 // Mocked static value to match UI mockup
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
