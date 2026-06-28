<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        $role = $request->query('role', 'employee');
        return view('login', compact('role'));
    }


    public function login(Request $request)
    {
        $request->validate([
            'loginId' => 'required',
            'password' => 'required'
        ]);

        $loginId = strtolower(trim($request->input('loginId')));
        $password = $request->input('password');

        \Illuminate\Support\Facades\Log::info("Login attempt: ID/Email: '{$loginId}', Password length: " . strlen($password));

        $expectedRole = $request->input('expected_role');

        if ($expectedRole === 'customer') {
           
            $user = User::whereRaw('LOWER(NID) = ?', [$loginId])
                        ->orWhereRaw('LOWER(CUSTOMER_ID) = ?', [$loginId])
                        ->first();
        } else {
           
            $user = User::whereRaw('LOWER(EMAIL) = ?', [$loginId])
                        ->orWhereRaw('LOWER(CUSTOMER_ID) = ?', [$loginId])
                        ->first();
        }

        if ($user) {
             \Illuminate\Support\Facades\Log::info("User array keys: " . implode(', ', array_keys($user->getAttributes())));
             
             
             $userPassword = $user->PASSWORD ?? $user->password;
             
             \Illuminate\Support\Facades\Log::info("Stored Password Hash: " . $userPassword);
             
             $hashCheck = Hash::check($password, $userPassword);
             \Illuminate\Support\Facades\Log::info("Hash check result: " . ($hashCheck ? "true" : "false"));
             
             if ($hashCheck) {
                if (($user->STATUS ?? $user->status) !== 'ACTIVE') {
                    return back()->withErrors(['login' => 'Your account is not active. Please contact support.']);
                }

                $role = strtoupper($user->ROLE ?? $user->role);
                $expectedRole = $request->input('expected_role');

                // Enforce role boundaries
                if ($expectedRole === 'customer' && $role !== 'CUSTOMER') {
                    return back()->withErrors(['login' => 'This login page is for customers only. Please use the Employee login.']);
                }
                
                if ($expectedRole === 'employee' && !in_array($role, ['EMPLOYEE', 'ADMIN'])) {
                    return back()->withErrors(['login' => 'This login page is for employees only. Please use the Customer login.']);
                }

                $remember = $request->has('remember');
                Auth::login($user, $remember);
                
               
                if ($role === 'EMPLOYEE' || $role === 'ADMIN') {
                    return redirect('/dashboard');
                } else {
                    return redirect('/user-dashboard');
                }
             }
        } else {
             \Illuminate\Support\Facades\Log::info("User NOT found for loginId: {$loginId}");
        }

        return back()->withErrors(['login' => 'Invalid ID/Email or password.']);
    }

  
    public function showSignup()
    {
        return view('signup');
    }

    public function signup(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string|max:50',
            'lastName' => 'required|string|max:50',
            'email' => 'required|email|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'dob' => 'required|date',
            'nid' => 'required|string|max:50',
            'password' => 'required|min:6|same:confirmPassword',
        ]);

      
        if (User::whereRaw('LOWER(EMAIL) = ?', [strtolower($request->email)])->exists()) {
            return back()->withErrors(['email' => 'An account with this email address already exists.'])->withInput();
        }

        $hashedPassword = Hash::make($request->password);
        $customerId = '';

       
        $firstName  = $request->input('firstName');
        $lastName   = $request->input('lastName');
        $email      = $request->input('email');
        $phone      = $request->input('phone');
        $address    = $request->input('address');
        $dob        = $request->input('dob');
        $nid        = $request->input('nid');

        try {
            
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN CREATE_CUSTOMER(:p_first_name, :p_last_name, :p_email, :p_phone, :p_address, TO_DATE(:p_dob, 'YYYY-MM-DD'), :p_nid, :p_password, 'CUSTOMER', 'ACTIVE', :o_customer_id); END;");

            $stmt->bindParam(':p_first_name', $firstName);
            $stmt->bindParam(':p_last_name',  $lastName);
            $stmt->bindParam(':p_email',      $email);
            $stmt->bindParam(':p_phone',      $phone);
            $stmt->bindParam(':p_address',    $address);
            $stmt->bindParam(':p_dob',        $dob);
            $stmt->bindParam(':p_nid',        $nid);
            $stmt->bindParam(':p_password',   $hashedPassword);

        
            $stmt->bindParam(':o_customer_id', $customerId, \PDO::PARAM_STR, 20);

            $stmt->execute();

            return redirect('/signup')->with('success', $customerId);

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Registration failed. ' . $e->getMessage()])->withInput();
        }
    }

   
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
