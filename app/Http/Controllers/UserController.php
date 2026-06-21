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
}
