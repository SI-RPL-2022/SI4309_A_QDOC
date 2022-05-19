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
        /**
     * Ini adalah action untuk halaman profil pasien
     *
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showProfile()
    {
        // Definisikan aturan otorisasi
        Gate::authorize('patient-page');

        return view('pasien.profile');
    }

    /**
     * Action untuk menampilkan halaman edit profil
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function editProfile()
    {
        Gate::authorize('patient-page');

        return view('pasien.edit-profile');
    }

    /**
     * Action untuk menangani proses edit profil
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function updateProfile(Request $request, $id)
    {
        // Definisikan aturan otorisasi
        Gate::authorize('patient-page');
        Gate::authorize('update-profile', intval($id));

        // Validasi data yang diterima
        $patientData = $request->validate([
            'name' => ['required'],
            'birth_place' => ['required'],
            'birth_date' => ['required', 'date_format:Y-m-d'],
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'phone_number' => ['required', 'phone:ID'],
        ]);

        // Format nomor hp
        $patientData['phone_number'] = PhoneNumber::make($patientData['phone_number'], 'ID')->formatInternational();

        // Ambil model user yang akan diedit
        $patient = User::find(intval($id));

        // Berikan data baru
        $patient->name = $patientData['name'];
        $patient->birth_place = $patientData['birth_place'];
        $patient->birth_date = $patientData['birth_date'];
        $patient->gender = $patientData['gender'];
        $patient->phone_number = $patientData['phone_number'];

        if ($patient->save()) {
            return response(
                json_encode([
                    'success' => true,
                ]),
                200
            );
        } else {
            return response(
                json_encode([
                    'success' => false,
                ]),
                500
            );
        }
    }
}