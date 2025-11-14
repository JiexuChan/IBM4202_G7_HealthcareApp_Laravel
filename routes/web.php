<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home'); // <-- This is the important change
});

Route::get('/login', function () {
    return view('login'); // This loads 'resources/views/login.blade.php'
});

// This route HANDLES the form submission
Route::post('/login-process', [AuthController::class, 'loginProcess']);

Route::get('/patient/dashboard', [PatientController::class, 'dashboard']);

Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/signup', [AuthController::class, 'showSignupStep1']);
Route::post('/signup-step1-process', [AuthController::class, 'handleSignupStep1']);

Route::get('/signup-step2', [AuthController::class, 'showSignupStep2']);
Route::post('/signup-step2-process', [AuthController::class, 'handleSignupStep2']);

Route::get('/patient/doctors', [PatientController::class, 'doctors']);

Route::get('/patient/schedule', [PatientController::class, 'schedule']);

Route::get('/patient/bookings', [PatientController::class, 'bookings']);
Route::get('/patient/bookings/cancel/{id}', [PatientController::class, 'cancelBooking']);

Route::get('/patient/settings', [PatientController::class, 'settings']);
Route::post('/patient/settings/update', [PatientController::class, 'updateSettings']);
Route::get('/patient/settings/delete', [PatientController::class, 'deleteAccount']);

Route::get('/patient/booking', [PatientController::class, 'showBooking']);
Route::post('/patient/booking-process', [PatientController::class, 'processBooking']);

Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);

Route::get('/admin/doctors', [AdminController::class, 'doctors']);

Route::get('/admin/doctors', [AdminController::class, 'doctors']);
Route::post('/admin/doctors/add', [AdminController::class, 'storeDoctor']);
Route::post('/admin/doctors/update', [AdminController::class, 'updateDoctor']);
Route::get('/admin/doctors/delete/{id}', [AdminController::class, 'deleteDoctor']);

Route::get('/admin/doctors', [AdminController::class, 'doctors']);
Route::post('/admin/doctors/add', [AdminController::class, 'storeDoctor']);
Route::post('/admin/doctors/update', [AdminController::class, 'updateDoctor']);
Route::get('/admin/doctors/delete/{id}', [AdminController::class, 'deleteDoctor']);

Route::get('/admin/schedule', [AdminController::class, 'schedule']);
Route::post('/admin/schedule/add', [AdminController::class, 'storeSession']);
Route::get('/admin/schedule/delete/{id}', [AdminController::class, 'deleteSession']);

Route::get('/admin/appointments', [AdminController::class, 'appointments']);
Route::get('/admin/appointments/delete/{id}', [AdminController::class, 'deleteAppointment']);

Route::get('/admin/patients', [AdminController::class, 'patients']);

Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard']);