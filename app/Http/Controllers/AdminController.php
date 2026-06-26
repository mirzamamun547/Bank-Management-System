<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Account;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Fetch pending accounts
        $pendingAccounts = Account::with('user')->where('status', 'Pending')->get();
        // Fetch active accounts for the existing accounts list
        $activeAccounts = Account::with('user')->where('status', '!=', 'Pending')->get();

        return view('admin.dashboard', compact('pendingAccounts', 'activeAccounts'));
    }

    public function approveAccount(Request $request, $id)
    {
        $account = Account::with('user')->findOrFail($id);
        
        if ($account->status !== 'Pending') {
            return back()->with('error', 'Account is not in a pending state.');
        }

        // Generate a random PIN for the account
        $pin = mt_rand(1000, 9999);

        DB::transaction(function () use ($account, $pin) {
            $account->status = 'Active';
            $account->save();

            // Create notification
            DB::table('notifications')->insert([
                'user_id' => $account->user_id,
                'message' => "Your bank account request for {$account->account_type} has been approved. Account No: {$account->account_number}, PIN: {$pin}",
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return back()->with('success', 'Account approved successfully. Notification sent to customer.');
    }
}
