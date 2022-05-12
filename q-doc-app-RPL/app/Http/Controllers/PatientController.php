<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Propaganistas\LaravelPhone\PhoneNumber;

class PatientController extends Controller
{

    /**
     * Ini adalah action untuk halaman beranda pasien
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        // Definisikan aturan otorisasi
        Gate::authorize('patient-page');

        return view('pasien.home');
    }
}