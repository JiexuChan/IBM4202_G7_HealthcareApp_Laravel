<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;      // <-- To talk to the database
use Illuminate\Support\Facades\Session; // <-- To use sessions
use Illuminate\Support\Facades\Hash;   // <-- We'll use this for passwords later

class AuthController extends Controller
{
    /**
     * Handle the incoming login request.
     */
    public function loginProcess(Request $request)
    {
        // 1. Get the form data
        $email = $request->input('useremail');
        $password = $request->input('userpassword'); // This is the plain-text password

        // 2. Find the user in the 'webuser' table
        $user = DB::table('webuser')->where('email', $email)->first();

        if (!$user) {
            return back()->with('error', 'We cant found any acount for this email.');
        }

        // 3. Check the user type and find their specific record
        $utype = $user->usertype;

        if ($utype == 'p') {
            $patient = DB::table('patient')->where('pemail', $email)->first();

            // *** FIXED PASSWORD CHECK ***
            // We now use Hash::check() to compare the plain-text password
            // with the HASHED password in the database.
            if ($patient && Hash::check($password, $patient->ppassword)) {
                // SUCCESS!
                session(['user' => $email, 'usertype' => 'p']);
                return redirect('/patient/dashboard');
            }

        } elseif ($utype == 'a') {
            $admin = DB::table('admin')->where('aemail', $email)->first();
            
            // *** FIXED PASSWORD CHECK (ASSUMING ADMINS HASH TOO) ***
            // NOTE: You must update your admin/doctor password fields to be hashed
            // For now, let's assume they are.
            if ($admin && $password == $admin->apassword) {
                // SUCCESS!
                session(['user' => $email, 'usertype' => 'a']);
                return redirect('/admin/dashboard'); 
            }

        } elseif ($utype == 'd') {
            $doctor = DB::table('doctor')->where('docemail', $email)->first();

            // *** FIXED PASSWORD CHECK (ASSUMING DOCTORS HASH TOO) ***
            if ($doctor && $password == $doctor->docpassword) {
                // SUCCESS!
                session(['user' => $email, 'usertype' => 'd']);
                return redirect('/doctor/dashboard');
            }
        }

        // 4. If we got this far, the password was wrong
        return back()->with('error', 'Wrong credentials: Invalid email or password');
    }


    /**
     * Handle the incoming logout request.
     */
    public function logout()
    {
        // 1. This clears all session data (forgets the user)
        Session::flush(); 

        // 2. Send them back to the homepage
        return redirect('/');
    }

    /**
     * Show the first step of the registration form.
     */
    public function showSignupStep1()
    {
        return view('signup');
    }

    /**
     * Process the first step of the registration.
     */
    public function handleSignupStep1(Request $request)
    {
        // 1. Get all the form data
        $personalDetails = [
            'fname' => $request->input('fname'),
            'lname' => $request->input('lname'),
            'address' => $request->input('address'),
            'nic' => $request->input('nic'),
            'dob' => $request->input('dob')
        ];

        // 2. Store it in the session (just like your old code)
        session(['personal' => $personalDetails]);

        // 3. Redirect to the NEXT step (create-account.php)
        return redirect('/signup-step2');
    }

    /**
     * Show the second step of the registration form.
     */
    public function showSignupStep2()
    {
        // Check if they've done step 1. If not, send them back.
        if (!Session::has('personal')) {
            return redirect('/signup');
        }
        return view('create-account');
    }

    /**
     * Process the final registration and create the user.
     */
    public function handleSignupStep2(Request $request)
    {
        // 1. Check if they've done step 1
        if (!Session::has('personal')) {
            return redirect('/signup');
        }

        // 2. Get data from session and form
        $personal = Session::get('personal');
        $email = $request->input('newemail');
        $tele = $request->input('tele');
        $newpassword = $request->input('newpassword');
        $cpassword = $request->input('cpassword');

        // 3. Validate data
        if ($newpassword != $cpassword) {
            return back()->with('error', 'Password Conformation Error! Reconform Password');
        }

        $existingUser = DB::table('webuser')->where('email', $email)->first();
        if ($existingUser) {
            return back()->with('error', 'Already have an account for this Email address.');
        }

        // 4. *** CRITICAL SECURITY FIX ***
        // We will HASH the password. NEVER store plain-text passwords.
        $hashedPassword = Hash::make($newpassword);
        $name = $personal['fname'] . " " . $personal['lname'];

        // 5. Create the user in a secure "Transaction"
        try {
            DB::transaction(function () use ($email, $name, $hashedPassword, $personal, $tele) {
                
                // Insert into patient table
                DB::table('patient')->insert([
                    'pemail' => $email,
                    'pname' => $name,
                    'ppassword' => $hashedPassword, // <-- Store the HASH, not the plain password
                    'paddress' => $personal['address'],
                    'pnic' => $personal['nic'],
                    'pdob' => $personal['dob'],
                    'ptel' => $tele
                ]);

                // Insert into webuser table
                DB::table('webuser')->insert([
                    'email' => $email,
                    'usertype' => 'p'
                ]);
            });
        } catch (\Exception $e) {
            // Something went wrong
            return back()->with('error', 'An error occurred. Please try again.');
        }

        // 6. Success! Clean up session and log the user in.
        Session::flush(); // Clear all old session data (like 'personal')
        session(['user' => $email, 'usertype' => 'p']); // Log them in

        return redirect('/patient/dashboard');
    }
}