<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route: /
// Route ini akan melakukan redirect ke halaman dokter atau pasien
// Sesuai dengan role dari user yang sudah login

Route::get('/', function (Request $request) {
    /**
     * @var \App\Models\User $user
     */
    $user = $request->user();

    if ($user->is_doctor) {
        return redirect('/dokter');
    } else {
        return redirect('/pasien');
    }
})->name('home')->middleware('auth');

// Route: /register
Route::get('/register', 'UserController@register')->name('register')->middleware('guest');
Route::post('/register', 'UserController@newUser')->name('new_user')->middleware('guest');

// Route: /login
Route::get('/login', 'UserController@login')->name('login')->middleware('guest');
Route::post('/login', 'UserController@authenticate')->name('auth')->middleware('guest');

// Route group khusus pasien
Route::prefix('pasien')->name('pasien.')->middleware('auth')->group(function () {

    // Route halaman beranda: /pasien
    Route::get('/', 'PatientController@index')->name('home');
    // Route group profile
    Route::prefix('profile')->name('profile.')->group(function () {
        // Route menampilkan profile: /pasien/profile
        Route::get('/', 'PatientController@showProfile')->name('show');

        // Route edit profile: /pasien/profile/edit
        Route::get('/edit', 'PatientController@editProfile')->name('edit');

        // Route update profile: /pasien/profile/{id}
        Route::put('/{id}', 'PatientController@updateProfile')->name('update');
    });
    // Route halaman booking: /pasien/booking
    Route::get('/booking', 'PatientController@showBooking')->name('booking');
    Route::post('/booking', 'PatientController@newBooking')->name('booking.new');
});

// Route grup khusus dokter
Route::prefix('dokter')->name('dokter.')->middleware('auth')->group(function () {

    // Route halaman beranda: /dokter
    Route::get('/', 'DoctorController@index')->name('home');
    // Route group halaman jadwal
    Route::prefix('jadwal')->name('jadwal.')->group(function () {
        // Route tabel jadwal: /dokter/jadwal/
        Route::get('/', 'DoctorController@showSchedules')->name('show');

        // Route tambah jadwal: /dokter/jadwal/
        Route::post('/', 'DoctorController@newSchedule')->name('new');

        // Route tambah jadwal mingguan (bulk): /dokter/jadwal/new/weekly
        Route::get('/new/weekly', 'DoctorController@addWeeklySchedule')->name('new.weekly');

        // Route menghapus jadwal lama: /dokter/jadwal/delete_past
        Route::get('/delete_past', 'DoctorController@deletePastSchedules')->name('delete.past');

        // Route menghapus jadwal: /dokter/jadwal/{id}
        Route::delete('/{schedule}', 'DoctorController@deleteSchedule')->name('delete');

        // Route mengganti status jadwal: /dokter/jadwal/{id}/toggle_status
        Route::put('/{schedule}/toggle_status', 'DoctorController@toggleStatus')->name('status.toggle');
    });

});