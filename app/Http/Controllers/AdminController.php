<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash; // <-- Make sure this is here

class AdminController extends Controller
{
    /**
     * Show the Admin Dashboard.
     */
    public function dashboard()
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'a') {
            return redirect('/login');
        }

        // 2. GET USER DATA (Hardcoded in your file)
        $useremail = 'admin@edoc.com';
        $username = 'Administrator';

        // 3. GET STATS
        $today = now()->toDateString();
        $doctor_count = DB::table('doctor')->count();
        $patient_count = DB::table('patient')->count();
        $appointment_count = DB::table('appointment')->where('appodate', '>=', $today)->count();
        $session_count = DB::table('schedule')->where('scheduledate', $today)->count();
        
        // 4. GET DOCTOR LIST FOR SEARCH
        $doctor_list = DB::table('doctor')->select('docname', 'docemail')->get();

        // 5. GET UPCOMING APPOINTMENTS
        $nextweek = now()->addWeek()->toDateString();
        $upcoming_appointments = DB::table('schedule')
            ->join('appointment', 'schedule.scheduleid', '=', 'appointment.scheduleid')
            ->join('patient', 'patient.pid', '=', 'appointment.pid')
            ->join('doctor', 'schedule.docid', '=', 'doctor.docid')
            ->where('schedule.scheduledate', '>=', $today)
            ->where('schedule.scheduledate', '<=', $nextweek)
            ->orderBy('schedule.scheduledate', 'desc')
            ->get();

        // 6. GET UPCOMING SESSIONS
        $upcoming_sessions = DB::table('schedule')
            ->join('doctor', 'schedule.docid', '=', 'doctor.docid')
            ->where('schedule.scheduledate', '>=', $today)
            ->where('schedule.scheduledate', '<=', $nextweek)
            ->orderBy('schedule.scheduledate', 'desc')
            ->get();
        
        // 7. SEND ALL DATA TO THE VIEW
        return view('admin.dashboard', [
            'username' => $username,
            'useremail' => $useremail,
            'today' => $today,
            'doctor_count' => $doctor_count,
            'patient_count' => $patient_count,
            'appointment_count' => $appointment_count,
            'session_count' => $session_count,
            'doctor_list' => $doctor_list,
            'upcoming_appointments' => $upcoming_appointments,
            'upcoming_sessions' => $upcoming_sessions,
        ]);
    }

    /**
     * Show the Admin's "Doctors" page (and all its popups).
     */
    public function doctors(Request $request)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'a') {
            return redirect('/login');
        }

        // 2. GET USER DATA (Hardcoded in your file)
        $useremail = 'admin@edoc.com';
        $username = 'Administrator';

        // 3. GET DATA FOR SEARCH/DROPDOWNS
        $doctor_list = DB::table('doctor')->select('docname', 'docemail')->get();
        $specialties = DB::table('specialties')->orderBy('sname', 'asc')->get();

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
        
        $doctor_count = $doctors->count();

        // 5. HANDLE POPUP MODALS
        $modalType = $request->input('action');
        $modalData = null;
        $error_code = $request->input('error', '0'); // Get error code from URL

        // Error message mapping (from your old file)
        $errorlist = [
            '1' => 'Already have an account for this Email address.',
            '2' => 'Password Conformation Error! Reconform Password',
            '3' => 'An error occurred. Please try again.',
            '4' => '', // Success
            '0' => '', // No error
        ];
        $error_message = $errorlist[$error_code] ?? '';

        if ($modalType == 'view' && $request->has('id')) {
            // Get data for the "View" popup
            $modalData = DB::table('doctor')
                ->join('specialties', 'doctor.specialties', '=', 'specialties.id')
                ->select('doctor.*', 'specialties.sname as spcil_name')
                ->where('doctor.docid', $request->input('id'))
                ->first();
        } 
        elseif ($modalType == 'edit' && $request->has('id')) {
            // Get data for the "Edit" popup
            $modalData = DB::table('doctor')
                ->join('specialties', 'doctor.specialties', '=', 'specialties.id')
                ->select('doctor.*', 'specialties.sname as spcil_name')
                ->where('doctor.docid', $request->input('id'))
                ->first();
        }
        elseif ($modalType == 'drop' && $request->has('id')) {
            // Get data for the "Drop" popup
            $modalData = [
                'id' => $request->input('id'),
                'name' => $request->input('name')
            ];
        }
        
        // 6. SEND ALL DATA TO THE VIEW
        return view('admin.doctors', [
            'username' => $username,
            'useremail' => $useremail,
            'today' => now()->toDateString(),
            'doctor_list' => $doctor_list,
            'doctors' => $doctors,
            'doctor_count' => $doctor_count,
            'specialties' => $specialties,
            'searchKeyword' => $searchKeyword,
            'modalType' => $modalType,
            'modalData' => $modalData,
            'error_message' => $error_message,
            'error_code' => $error_code,
        ]);
    }

    /**
     * Process the "Add New Doctor" form.
     * (Logic from add-new.php)
     */
    public function storeDoctor(Request $request)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'a') {
            return redirect('/login');
        }

        // 2. VALIDATION
        if ($request->input('password') != $request->input('cpassword')) {
            // Password mismatch
            return redirect('/admin/doctors?action=add&error=2');
        }

        $existingUser = DB::table('webuser')->where('email', $request->input('email'))->first();
        if ($existingUser) {
            // Email already exists
            return redirect('/admin/doctors?action=add&error=1');
        }

        // 3. CREATE DOCTOR (with HASHED password)
        try {
            DB::transaction(function () use ($request) {
                // *** SECURITY FIX: Hash the password ***
                $hashedPassword = Hash::make($request->input('password'));

                // Insert into doctor table
                DB::table('doctor')->insert([
                    'docemail' => $request->input('email'),
                    'docname' => $request->input('name'),
                    'docpassword' => $hashedPassword,
                    'docnic' => $request->input('nic'),
                    'doctel' => $request->input('Tele'),
                    'specialties' => $request->input('spec')
                ]);

                // Insert into webuser table
                DB::table('webuser')->insert([
                    'email' => $request->input('email'),
                    'usertype' => 'd'
                ]);
            });
        } catch (\Exception $e) {
            // Database error
            return redirect('/admin/doctors?action=add&error=3');
        }

        // 4. SUCCESS
        return redirect('/admin/doctors?action=add&error=4');
    }

    /**
     * Process the "Edit Doctor" form.
     * (Logic from edit-doc.php)
     */
    public function updateDoctor(Request $request)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'a') {
            return redirect('/login');
        }

        // 2. GET DATA
        $id = $request->input('id00');
        $email = $request->input('email');
        $oldemail = $request->input('oldemail');
        $password = $request->input('password');
        $cpassword = $request->input('cpassword');

        // 3. VALIDATION
        if ($password != $cpassword) {
            // Password mismatch
            return redirect('/admin/doctors?action=edit&error=2&id='.$id);
        }

        // Check if new email is already taken by ANOTHER user
        $existingUser = DB::table('webuser')->where('email', $email)->where('email', '!=', $oldemail)->first();
        if ($existingUser) {
            // Email already exists
            return redirect('/admin/doctors?action=edit&error=1&id='.$id);
        }

        // 4. UPDATE DOCTOR (with HASHED password)
        try {
            DB::transaction(function () use ($request, $id, $email, $oldemail) {
                // *** SECURITY FIX: Hash the password ***
                $hashedPassword = Hash::make($request->input('password'));

                // Update doctor table
                DB::table('doctor')
                    ->where('docid', $id)
                    ->update([
                        'docemail' => $email,
                        'docname' => $request->input('name'),
                        'docpassword' => $hashedPassword,
                        'docnic' => $request->input('nic'),
                        'doctel' => $request->input('Tele'),
                        'specialties' => $request->input('spec')
                    ]);

                // Update webuser table
                DB::table('webuser')
                    ->where('email', $oldemail)
                    ->update(['email' => $email]);
            });
        } catch (\Exception $e) {
            // Database error
            return redirect('/admin/doctors?action=edit&error=3&id='.$id);
        }

        // 5. SUCCESS
        return redirect('/admin/doctors?action=edit&error=4&id='.$id);
    }

    /**
     * Process the "Delete Doctor" action.
     * (Logic from delete-doctor.php)
     */
    public function deleteDoctor($id)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'a') {
            return redirect('/login');
        }

        // 2. GET DOCTOR EMAIL before deleting
        $doctor = DB::table('doctor')->where('docid', $id)->first();
        if (!$doctor) {
            // Doctor not found, just go back
            return redirect('/admin/doctors');
        }
        $email = $doctor->docemail;

        // 3. DELETE in a transaction
        try {
            DB::transaction(function () use ($id, $email) {
                DB::table('webuser')->where('email', $email)->delete();
                DB::table('doctor')->where('docid', $id)->delete();
                // We should also delete their sessions and appointments, but we'll follow your old logic for now.
            });
        } catch (\Exception $e) {
            // Database error
            return redirect('/admin/doctors')->with('error', 'Could not delete doctor.');
        }

        // 4. SUCCESS
        return redirect('/admin/doctors');
    }

    public function schedule(Request $request)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'a') {
            return redirect('/login');
        }

        // 2. GET USER DATA
        $useremail = 'admin@edoc.com';
        $username = 'Administrator';
        $today = now()->toDateString();

        // 3. GET DATA FOR FILTERS/POPUPS
        $doctor_list = DB::table('doctor')->orderBy('docname', 'asc')->get();
        $session_count = DB::table('schedule')->count();

        // 4. GET MAIN SCHEDULE LIST (with filters)
        $filter_date = $request->input('sheduledate');
        $filter_docid = $request->input('docid');

        $sessions = DB::table('schedule')
            ->join('doctor', 'schedule.docid', '=', 'doctor.docid')
            ->select('schedule.scheduleid', 'schedule.title', 'doctor.docname', 'schedule.scheduledate', 'schedule.scheduletime', 'schedule.nop')
            ->when($filter_date, function ($query, $date) {
                $query->where('schedule.scheduledate', $date);
            })
            ->when($filter_docid, function ($query, $docid) {
                $query->where('doctor.docid', $docid);
            })
            ->orderBy('schedule.scheduledate', 'desc')
            ->get();
        
        // 5. HANDLE POPUP MODALS
        $modalType = $request->input('action');
        $modalData = null;
        $modalPatients = null;

        if ($modalType == 'view' && $request->has('id')) {
            // Get data for the "View" popup
            $modalData = DB::table('schedule')
                ->join('doctor', 'schedule.docid', '=', 'doctor.docid')
                ->where('schedule.scheduleid', $request->input('id'))
                ->first();
            
            // Get patients for this session
            $modalPatients = DB::table('appointment')
                ->join('patient', 'patient.pid', '=', 'appointment.pid')
                ->where('appointment.scheduleid', $request->input('id'))
                ->get();
        }
        elseif ($modalType == 'drop' && $request->has('id')) {
            // Get data for the "Drop" popup
            $modalData = [
                'id' => $request->input('id'),
                'name' => $request->input('name')
            ];
        }
        
        // 6. SEND ALL DATA TO THE VIEW
        return view('admin.schedule', [
            'username' => $username,
            'useremail' => $useremail,
            'today' => $today,
            'doctor_list' => $doctor_list,
            'session_count' => $session_count,
            'sessions' => $sessions,
            'modalType' => $modalType,
            'modalData' => $modalData,
            'modalPatients' => $modalPatients,
        ]);
    }

    /**
     * Process the "Add New Session" form.
     * (Logic from add-session.php)
     */
    public function storeSession(Request $request)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'a') {
            return redirect('/login');
        }

        // 2. CREATE SESSION
        try {
            DB::table('schedule')->insert([
                'docid' => $request->input('docid'),
                'title' => $request->input('title'),
                'nop' => $request->input('nop'),
                'scheduledate' => $request->input('date'),
                'scheduletime' => $request->input('time')
            ]);
        } catch (\Exception $e) {
            // Database error
            return redirect('/admin/schedule')->with('error', 'Could not add session.');
        }

        // 4. SUCCESS
        return redirect('/admin/schedule?action=session-added&title=' . $request->input('title'));
    }

    /**
     * Process the "Delete Session" action.
     * (Logic from delete-session.php)
     */
    public function deleteSession($id)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'a') {
            return redirect('/login');
        }
        
        // 2. DELETE (We should also delete appointments, but following old logic)
        try {
            DB::table('schedule')->where('scheduleid', $id)->delete();
        } catch (\Exception $e) {
            // Database error
            return redirect('/admin/schedule')->with('error', 'Could not delete session.');
        }

        // 4. SUCCESS
        return redirect('/admin/schedule');
    }

    public function appointments(Request $request)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'a') {
            return redirect('/login');
        }

        // 2. GET USER DATA
        $useremail = 'admin@edoc.com';
        $username = 'Administrator';
        $today = now()->toDateString();
        
        // 3. GET DATA FOR FILTERS
        $doctor_list = DB::table('doctor')->orderBy('docname', 'asc')->get();
        $appointment_count = DB::table('appointment')->count();

        // 4. GET APPOINTMENTS (with filters)
        $filter_date = $request->input('sheduledate');
        $filter_docid = $request->input('docid');

        $appointments = DB::table('schedule')
            ->join('appointment', 'schedule.scheduleid', '=', 'appointment.scheduleid')
            ->join('patient', 'patient.pid', '=', 'appointment.pid')
            ->join('doctor', 'schedule.docid', '=', 'doctor.docid')
            ->select('appointment.appoid', 'schedule.title', 'doctor.docname', 'patient.pname', 'schedule.scheduledate', 'schedule.scheduletime', 'appointment.apponum', 'appointment.appodate')
            ->when($filter_date, function ($query, $date) {
                $query->where('schedule.scheduledate', $date);
            })
            ->when($filter_docid, function ($query, $docid) {
                $query->where('doctor.docid', $docid);
            })
            ->orderBy('schedule.scheduledate', 'desc')
            ->get();
        
        // 5. SEND ALL DATA TO THE VIEW
        return view('admin.appointments', [
            'username' => $username,
            'useremail' => $useremail,
            'today' => $today,
            'doctor_list' => $doctor_list,
            'appointment_count' => $appointment_count,
            'appointments' => $appointments,
        ]);
    }

    /**
     * Process the "Delete Appointment" action.
     * (Logic from delete-appointment.php)
     */
    public function deleteAppointment($id)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'a') {
            return redirect('/login');
        }
        
        // 2. DELETE
        try {
            DB::table('appointment')->where('appoid', $id)->delete();
        } catch (\Exception $e) {
            // Database error
            return redirect('/admin/appointments')->with('error', 'Could not delete appointment.');
        }

        // 4. SUCCESS
        return redirect('/admin/appointments');
    }

    public function patients(Request $request)
    {
        // 1. SECURITY CHECK
        if (!Session::has('user') || Session::get('usertype') != 'a') {
            return redirect('/login');
        }

        // 2. GET USER DATA
        $useremail = 'admin@edoc.com';
        $username = 'Administrator';
        $today = now()->toDateString();
        
        // 3. GET DATA FOR DATALIST
        $patient_list = DB::table('patient')->select('pname', 'pemail')->get();

        // 4. GET MAIN PATIENT LIST (with search)
        $searchKeyword = $request->input('search');

        $patients = DB::table('patient')
            ->when($searchKeyword, function ($query, $keyword) {
                // This is the Laravel way to do your complex search
                $query->where('pemail', 'like', "%{$keyword}%")
                      ->orWhere('pname', 'like', "%{$keyword}%");
            })
            ->orderBy('pid', 'desc')
            ->get();
        
        $patient_count = $patient_list->count(); // Use the total count for the title

        // 5. HANDLE POPUP MODAL
        $modalType = $request->input('action');
        $modalData = null;

        if ($modalType == 'view' && $request->has('id')) {
            // Get data for the "View" popup
            $modalData = DB::table('patient')
                ->where('pid', $request->input('id'))
                ->first();
        }
        
        // 6. SEND ALL DATA TO THE VIEW
        return view('admin.patients', [
            'username' => $username,
            'useremail' => $useremail,
            'today' => $today,
            'patient_list' => $patient_list,
            'patients' => $patients,
            'patient_count' => $patient_count,
            'modalType' => $modalType,
            'modalData' => $modalData,
        ]);
    }
}