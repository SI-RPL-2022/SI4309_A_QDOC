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
});

// Route grup khusus dokter
Route::prefix('dokter')->name('dokter.')->middleware('auth')->group(function () {

    // Route halaman beranda: /dokter
    Route::get('/', 'DoctorController@index')->name('home');
});