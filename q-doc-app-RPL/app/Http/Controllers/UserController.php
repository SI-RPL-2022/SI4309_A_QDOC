<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\PhoneNumber;

class UserController extends Controller
{
    /**
     * Action untuk menampilkan halaman register
     *
     * @return \Illuminate\View\View
     */
    public function register()
    {
        return view('auth.register');
    }

    /**
     * Action untuk menangani proses register
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function newUser(Request $request)
    {
        $userData = $request->validate([
            'name' => ['required'],
            'birth_place' => ['required'],
            'birth_date' => ['required', 'date_format:Y-m-d'],
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'phone_number' => ['required', 'phone:ID'],
            'password' => ['required', 'confirmed'],
        ]);

        $userData['phone_number'] = PhoneNumber::make($userData['phone_number'], 'ID')->formatInternational();
        
        $newValidator = Validator::make($userData, [
            'phone_number' => ['unique:users,phone_number']
        ]);
        if ($newValidator->fails()) {
            return back()
                ->withErrors($newValidator)
                ->withInput();
        }

        $newUser = new User($userData);

        if ($newUser->save()) {
            Auth::login($newUser);

            $request->session()->regenerate();

            return redirect('/');
        } else {
            return back()->withErrors([
                'registerError' => 'Proses registrasi gagal.',
            ]);
        }
    }
       /**
     * Action untuk menangani proses login
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(Request $request)
    {

        // Validasi data login
        $credentials = $request->validate([
            'phone_number' => ['required', 'phone:ID'],
            'password' => ['required'],
        ]);

        // Format no. hp agar semua no. hp sama formatnya dalam database
        $credentials['phone_number'] = PhoneNumber::make($credentials['phone_number'], 'ID')->formatInternational();

        // Mulai proses autentikasi
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect ke halaman yang hendak diakses sebelum login atau ke route /
            return redirect()->intended();
        }

        // Jika error kembali ke halaman login dan flash pesan error
        return back()->withErrors([
            'loginError' => 'Nomor HP atau password salah.'
        ]);
    }

    /**
     * Action untuk menampilkan halaman login
     *
     * @return \Illuminate\View\View
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Action untuk menangani proses logout
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}