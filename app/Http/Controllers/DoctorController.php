<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DoctorController extends Controller
{
    /**
     * Show the Doctor Dashboard.
     */
    public function dashboard()
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'd') {
            return redirect('/login');
        }

        // 2. GET USER DATA
        $useremail = Session::get('user');
        $user = DB::table('doctor')->where('docemail', $useremail)->first();
        if (!$user) {
            Session::flush();
            return redirect('/login')->with('error', 'Doctor account not found.');
        }
        $username = $user->docname;
        $userid = $user->docid;

        // 3. GET STATS (from your index.php)
        $today = now()->toDateString();
        $doctor_count = DB::table('doctor')->count();
        $patient_count = DB::table('patient')->count();
        $appointment_count = DB::table('appointment')->where('appodate', '>=', $today)->count();
        $session_count = DB::table('schedule')->where('scheduledate', $today)->count();

        // 4. GET UPCOMING SESSIONS (from your index.php)
        $nextweek = now()->addWeek()->toDateString();
        $upcoming_sessions = DB::table('schedule')
            ->join('doctor', 'schedule.docid', '=', 'doctor.docid')
            ->where('schedule.docid', $userid) // <-- Only for this doctor
            ->where('schedule.scheduledate', '>=', $today)
            ->where('schedule.scheduledate', '<=', $nextweek)
            ->orderBy('schedule.scheduledate', 'desc')
            ->get();

        // 5. SEND ALL DATA TO THE VIEW
        return view('doctor.dashboard', [
            'username' => $username,
            'useremail' => $useremail,
            'today' => $today,
            'doctor_count' => $doctor_count,
            'patient_count' => $patient_count,
            'appointment_count' => $appointment_count,
            'session_count' => $session_count,
            'upcoming_sessions' => $upcoming_sessions,
        ]);
    }
}