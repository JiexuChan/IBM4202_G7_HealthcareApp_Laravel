<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PatientController extends Controller
{
    public function dashboard()
    {
        // 1. SECURITY CHECK (from your old file)
        if (!Session::has('user') || Session::get('usertype') != 'p') {
            // If not logged in as a patient, kick them to login
            return redirect('/login');
        }

        // 2. GET USER DATA (from your old file)
        $useremail = Session::get('user');
        $user = DB::table('patient')->where('pemail', $useremail)->first();

        // If user not found (e.g., deleted), log them out
        if (!$user) {
            Session::flush();
            return redirect('/login')->with('error', 'User not found.');
        }

        $userid = $user->pid;
        $username = $user->pname;

        // 3. GET ALL OTHER DATA (all your other queries)
        $today = now()->toDateString(); // 'Y-m-d' format

        // Stats
        $doctor_count = DB::table('doctor')->count();
        $patient_count = DB::table('patient')->count();
        $appointment_count = DB::table('appointment')->where('appodate', '>=', $today)->count();
        $session_count = DB::table('schedule')->where('scheduledate', $today)->count();

        // Doctor list for search bar
        $doctor_list = DB::table('doctor')->select('docname')->get();

        // Upcoming bookings
        $bookings = DB::table('schedule')
            ->join('appointment', 'schedule.scheduleid', '=', 'appointment.scheduleid')
            ->join('patient', 'patient.pid', '=', 'appointment.pid')
            ->join('doctor', 'schedule.docid', '=', 'doctor.docid')
            ->where('patient.pid', $userid)
            ->where('schedule.scheduledate', '>=', $today)
            ->orderBy('schedule.scheduledate', 'asc')
            ->get();
        
        // 4. SEND ALL DATA TO THE VIEW
        return view('patient.dashboard', [
            'username' => $username,
            'useremail' => $useremail,
            'today' => $today,
            'doctor_count' => $doctor_count,
            'patient_count' => $patient_count,
            'appointment_count' => $appointment_count,
            'session_count' => $session_count,
            'doctor_list' => $doctor_list,
            'bookings' => $bookings,
        ]);
    }

    /**
     * Show the All Doctors page.
     */
    public function doctors(Request $request)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'p') {
            return redirect('/login');
        }

        // 2. GET USER DATA
        $useremail = Session::get('user');
        $user = DB::table('patient')->where('pemail', $useremail)->first();
        if (!$user) {
            Session::flush();
            return redirect('/login')->with('error', 'User not found.');
        }
        $username = $user->pname;

        // 3. GET DATA FOR SEARCH BAR DATALIST
        $doctor_list = DB::table('doctor')->select('docname', 'docemail')->get();

        // 4. GET MAIN DOCTOR LIST (with search)
        $searchKeyword = $request->input('search');
        
        $doctors = DB::table('doctor')
            ->join('specialties', 'doctor.specialties', '=', 'specialties.id')
            ->select('doctor.*', 'specialties.sname as spcil_name') // Get specialty name
            ->when($searchKeyword, function ($query, $keyword) {
                // This is the Laravel way to do your complex search
                $query->where('doctor.docname', 'like', "%{$keyword}%")
                      ->orWhere('doctor.docemail', 'like', "%{$keyword}%");
            })
            ->orderBy('doctor.docid', 'desc')
            ->get();

        // 5. HANDLE POPUP MODALS
        $modalType = $request->input('action');
        $modalData = null;

        if ($modalType == 'view' && $request->has('id')) {
            // Get data for the "View" popup
            $modalData = DB::table('doctor')
                ->join('specialties', 'doctor.specialties', '=', 'specialties.id')
                ->select('doctor.*', 'specialties.sname as spcil_name')
                ->where('doctor.docid', $request->input('id'))
                ->first();
        } 
        elseif ($modalType == 'session' && $request->has('id')) {
            // Get data for the "Session" popup
            $modalData = [
                'id' => $request->input('id'),
                'name' => $request->input('name')
            ];
        }
        // Note: We are ignoring 'drop' and 'edit' actions, 
        // as patients should not have permission to do that. This is a security fix.

        // 6. SEND ALL DATA TO THE VIEW
        return view('patient.doctors', [
            'username' => $username,
            'useremail' => $useremail,
            'today' => now()->toDateString(),
            'doctor_list' => $doctor_list,
            'doctors' => $doctors,
            'searchKeyword' => $searchKeyword,
            'modalType' => $modalType,
            'modalData' => $modalData,
        ]);
    }

    /**
     * Show the Scheduled Sessions page.
     */
    public function schedule(Request $request)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'p') {
            return redirect('/login');
        }

        // 2. GET USER DATA
        $useremail = Session::get('user');
        $user = DB::table('patient')->where('pemail', $useremail)->first();
        if (!$user) {
            Session::flush();
            return redirect('/login')->with('error', 'User not found.');
        }
        $username = $user->pname;

        // 3. GET DATA FOR SEARCH BAR DATALIST
        $doctor_list = DB::table('doctor')->select('docname')->distinct()->get();
        $session_list = DB::table('schedule')->select('title')->distinct()->get();

        // 4. GET MAIN SESSION LIST (with search)
        $today = now()->toDateString();
        $searchKeyword = $request->input('search');

        $sessions = DB::table('schedule')
            ->join('doctor', 'schedule.docid', '=', 'doctor.docid')
            ->where('schedule.scheduledate', '>=', $today)
            ->when($searchKeyword, function ($query, $keyword) {
                // This is the Laravel way to do your complex search
                $query->where('doctor.docname', 'like', "%{$keyword}%")
                      ->orWhere('schedule.title', 'like', "%{$keyword}%")
                      ->orWhere('schedule.scheduledate', 'like', "%{$keyword}%");
            })
            ->orderBy('schedule.scheduledate', 'asc')
            ->get();
        
        // 5. SEND ALL DATA TO THE VIEW
        return view('patient.schedule', [
            'username' => $username,
            'useremail' => $useremail,
            'today' => $today,
            'doctor_list' => $doctor_list,
            'session_list' => $session_list,
            'sessions' => $sessions,
            'searchKeyword' => $searchKeyword,
        ]);
    }

    /**
     * Show the "My Bookings" page.
     */
    public function bookings(Request $request)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'p') {
            return redirect('/login');
        }

        // 2. GET USER DATA
        $useremail = Session::get('user');
        $user = DB::table('patient')->where('pemail', $useremail)->first();
        if (!$user) {
            Session::flush();
            return redirect('/login')->with('error', 'User not found.');
        }
        $username = $user->pname;
        $userid = $user->pid;

        // 3. GET BOOKINGS (with filter)
        $sheduledate = $request->input('sheduledate'); // Get date from filter

        $bookingsQuery = DB::table('schedule')
            ->join('appointment', 'schedule.scheduleid', '=', 'appointment.scheduleid')
            ->join('patient', 'patient.pid', '=', 'appointment.pid')
            ->join('doctor', 'schedule.docid', '=', 'doctor.docid')
            ->where('patient.pid', $userid)
            ->select('appointment.appoid', 'schedule.title', 'doctor.docname', 'schedule.scheduledate', 'schedule.scheduletime', 'appointment.apponum', 'appointment.appodate');

        // If the user filtered by date, add it to the query
        if ($sheduledate) {
            $bookingsQuery->where('schedule.scheduledate', $sheduledate);
        }

        $bookings = $bookingsQuery->orderBy('appointment.appodate', 'asc')->get();

        // 4. SEND ALL DATA TO THE VIEW
        return view('patient.bookings', [
            'username' => $username,
            'useremail' => $useremail,
            'today' => now()->toDateString(),
            'bookings' => $bookings,
            'sheduledate' => $sheduledate, // Send the filter date back
        ]);
    }

    /**
     * Cancel a booking (logic from delete-appointment.php)
     */
    public function cancelBooking($id)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'p') {
            return redirect('/login');
        }

        // 2. GET USER ID
        $useremail = Session::get('user');
        $user = DB::table('patient')->where('pemail', $useremail)->first();
        $userid = $user->pid;

        // 3. SECURELY DELETE THE APPOINTMENT
        // Make sure the appointment belongs to the logged-in user
        $deleted = DB::table('appointment')
            ->where('appoid', $id)
            ->where('pid', $userid) // <-- This is the important security check
            ->delete();

        if ($deleted) {
            return redirect('/patient/bookings')->with('success', 'Your booking has been canceled.');
        }

        // If nothing was deleted, just go back
        return redirect('/patient/bookings')->with('error', 'Could not cancel the booking.');
    }

    /**
     * Show the Settings page and handle popups.
     */
    public function settings(Request $request)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'p') {
            return redirect('/login');
        }

        // 2. GET USER DATA
        $useremail = Session::get('user');
        $user = DB::table('patient')->where('pemail', $useremail)->first();
        if (!$user) {
            Session::flush();
            return redirect('/login')->with('error', 'User not found.');
        }
        $username = $user->pname;
        $userid = $user->pid;
        
        // 3. HANDLE POPUP MODALS
        $modalType = $request->input('action');
        $modalData = $user; // For 'view' and 'edit', the data is just the user
        $error = $request->input('error'); // For the edit form

        // 4. SEND ALL DATA TO THE VIEW
        return view('patient.settings', [
            'username' => $username,
            'useremail' => $useremail,
            'today' => now()->toDateString(),
            'user' => $user, // Send the full user object
            'modalType' => $modalType,
            'modalData' => $modalData,
            'error_code' => $error,
        ]);
    }

    /**
     * Update user settings (logic from edit-user.php)
     */
    public function updateSettings(Request $request)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'p') {
            return redirect('/login');
        }

        // 2. GET DATA
        $id = $request->input('id00');
        $oldemail = $request->input('oldemail');
        $email = $request->input('email');
        $name = $request->input('name');
        $nic = $request->input('nic');
        $tele = $request->input('Tele');
        $address = $request->input('address');
        $password = $request->input('password');
        $cpassword = $request->input('cpassword');

        // 3. GET USER ID from session to be safe
        $session_email = Session::get('user');
        $user = DB::table('patient')->where('pemail', $session_email)->first();
        
        // Security check: Make sure the user is editing their OWN profile
        if ($user->pid != $id) {
            return redirect('/patient/settings')->with('error', 'Unauthorized action.');
        }

        // 4. VALIDATION (from your old file)
        if ($password != $cpassword) {
            return redirect('/patient/settings?action=edit&id='.$id.'&error=2');
        }

        // Check if new email is already taken by ANOTHER user
        $existingUser = DB::table('webuser')->where('email', $email)->where('email', '!=', $oldemail)->first();
        if ($existingUser) {
            return redirect('/patient/settings?action=edit&id='.$id.'&error=1');
        }
        
        // 5. *** SECURITY FIX: HASH THE NEW PASSWORD ***
        $hashedPassword = Hash::make($password);

        // 6. UPDATE DATABASE in a transaction
        try {
            DB::transaction(function () use ($id, $email, $oldemail, $name, $hashedPassword, $nic, $tele, $address) {
                
                // Update patient table
                DB::table('patient')
                    ->where('pid', $id)
                    ->update([
                        'pemail' => $email,
                        'pname' => $name,
                        'ppassword' => $hashedPassword,
                        'pnic' => $nic,
                        'ptel' => $tele,
                        'paddress' => $address
                    ]);

                // Update webuser table
                DB::table('webuser')
                    ->where('email', $oldemail)
                    ->update(['email' => $email]);
            });
        } catch (\Exception $e) {
            // Something went wrong
            return redirect('/patient/settings?action=edit&id='.$id.'&error=3');
        }

        // 7. SUCCESS
        // Force user to log in again if they changed their email
        if ($oldemail != $email) {
            Session::flush();
            return redirect('/login')->with('success', 'Settings updated! Please log in with your new email.');
        }
        
        // Update session email
        session(['user' => $email]);
        return redirect('/patient/settings?action=edit-success');
    }

    /**
     * Delete user account (logic from delete-account.php)
     */
    public function deleteAccount()
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'p') {
            return redirect('/login');
        }

        // 2. GET USER from session
        $useremail = Session::get('user');
        
        // 3. DELETE in a transaction
        try {
            DB::transaction(function () use ($useremail) {
                DB::table('patient')->where('pemail', $useremail)->delete();
                DB::table('webuser')->where('email', $useremail)->delete();
                // Note: You might want to also delete their appointments
            });
        } catch (\Exception $e) {
            // Something went wrong
            return redirect('/patient/settings')->with('error', 'Could not delete account.');
        }
        
        // 4. SUCCESS: Log them out and send to homepage
        Session::flush();
        return redirect('/')->with('success', 'Your account has been deleted.');
    }

    /**
     * Show the booking confirmation page.
     */
    public function showBooking(Request $request)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'p') {
            return redirect('/login');
        }

        // 2. GET USER DATA
        $useremail = Session::get('user');
        $user = DB::table('patient')->where('pemail', $useremail)->first();
        if (!$user) {
            Session::flush();
            return redirect('/login')->with('error', 'User not found.');
        }
        $username = $user->pname;

        // 3. GET SCHEDULE DATA from the URL
        $schedule_id = $request->input('id');
        $session = DB::table('schedule')
            ->join('doctor', 'schedule.docid', '=', 'doctor.docid')
            ->where('schedule.scheduleid', $schedule_id)
            ->first();

        if (!$session) {
            // No session found, send them back
            return redirect('/patient/schedule')->with('error', 'Session not found.');
        }

        // 4. GET APPOINTMENT NUMBER
        $apponum = DB::table('appointment')
            ->where('scheduleid', $schedule_id)
            ->count() + 1;
        
        // 5. SEND ALL DATA TO THE VIEW
        return view('patient.booking', [
            'username' => $username,
            'useremail' => $useremail,
            'today' => now()->toDateString(),
            'session' => $session,
            'apponum' => $apponum,
        ]);
    }

    /**
     * Process the new booking (logic from booking-complete.php)
     */
    public function processBooking(Request $request)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'p') {
            return redirect('/login');
        }

        // 2. GET USER ID
        $useremail = Session::get('user');
        $user = DB::table('patient')->where('pemail', $useremail)->first();
        $userid = $user->pid;

        // 3. GET FORM DATA
        $apponum = $request->input('apponum');
        $scheduleid = $request->input('scheduleid');
        $date = $request->input('date');

        // 4. INSERT INTO DATABASE
        try {
            DB::table('appointment')->insert([
                'pid' => $userid,
                'apponum' => $apponum,
                'scheduleid' => $scheduleid,
                'appodate' => $date
            ]);
        } catch (\Exception $e) {
            // Something went wrong
            return redirect('/patient/schedule')->with('error', 'Booking failed! Please try again.');
        }

        // 5. SUCCESS! Redirect to "My Bookings" with a success message
        // We will use a session flash message to trigger the success popup
        return redirect('/patient/bookings')->with('booking_success_id', $apponum);
    }
}