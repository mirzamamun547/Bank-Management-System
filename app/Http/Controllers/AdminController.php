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

        // Fetch all customers (needed for the Customers section of the dashboard)
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
        if (!$customer) {
            abort(404, 'Customer not found.');
        }

        return view('admin.edit-customer', compact('customer'));
    }

    public function updateCustomer(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:USERS,EMAIL,' . $id,
            'phone' => 'nullable|string|max:20',
            'nid' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'status' => 'required|string|in:ACTIVE,SUSPENDED',
        ]);

        DB::table('USERS')->where('id', $id)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'nid' => $request->nid,
            'address' => $request->address,
            'dob' => $request->dob ? \Carbon\Carbon::parse($request->dob) : null,
            'status' => $request->status,
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
