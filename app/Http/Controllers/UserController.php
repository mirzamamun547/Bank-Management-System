<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    // Show profile page
    public function showProfile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    // Update profile details
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // Map frontend inputs to Oracle column names (which are handled dynamically by Eloquent)
        // Oracle usually expects upper case column names for updates if not handled by lowercase mapping.
        // Laravel's array attributes will handle lowercase since we mapped the primary key.
        // The trigger updates FULL_NAME automatically on INSERT, but not UPDATE.
        // Let's update FULL_NAME as well just in case.
        
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
            'new_password' => 'required|min:6|confirmed', // expects new_password_confirmation field
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

        return view('user.accounts', compact('accounts'));
    }

    public function storeAccount(Request $request)
    {
        $request->validate([
            'account_type' => 'required|string|in:Saving,Current,FD',
            'opening_balance' => 'required|numeric|min:0',
            'father_name' => 'nullable|string|max:100',
            'mother_name' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:Male,Female,Transgender',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'profile_photo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        
        // Update user details
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
        
        // Map account type
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

        $user->accounts()->create([
            'account_number' => $account_number,
            'account_type' => $mappedType,
            'balance' => $request->opening_balance,
            'status' => 'Pending',
            'branch' => 'Central Branch',
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
}
