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
        // 1. Branch Report (using BRANCH_SUMMARY_VIEW)
        $branchReport = DB::table('BRANCH_SUMMARY_VIEW')->get();

        // 2. Support Tickets
        $userReports = DB::table('support_tickets')
            ->join('USERS', 'support_tickets.user_id', '=', 'USERS.id')
            ->select('support_tickets.*', 'USERS.first_name', 'USERS.last_name', 'USERS.role', 'USERS.customer_id')
            ->orderBy('support_tickets.created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'branchReport',
            'userReports'
        ))->with('section', 'reports');
    }

    public function respondTicket(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Open,In Review,Resolved,Closed',
            'admin_response' => 'nullable|string',
        ]);

        DB::table('support_tickets')->where('id', $id)->update([
            'status' => $request->status,
            'admin_response' => $request->admin_response,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.reports')->with('success', 'Ticket updated and notification generated via PL/SQL Trigger.');
    }
}

