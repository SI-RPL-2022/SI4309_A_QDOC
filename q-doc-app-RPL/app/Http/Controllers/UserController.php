<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}