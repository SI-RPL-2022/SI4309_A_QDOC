<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Schedule;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{

    /**
     * Ini adalah action untuk halaman beranda dokter
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        // Definisikan aturan otorisasi
        Gate::authorize('doctor-page');

        return view('dokter.home');
    }
}