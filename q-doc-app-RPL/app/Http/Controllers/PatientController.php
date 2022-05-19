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
        /**
     * Action untuk menampilkan halaman booking
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showBooking(Request $request)
    {
        // Definisikan aturan otorisasi
        Gate::authorize('patient-page');

        // Ambil tanggal dan waktu sekarang
        $curDate = now('Asia/Jakarta')->toDateString();
        $curTime = now('Asia/Jakarta')->toTimeString();

        // Ambil konsultasi pasien yang belum selesai jika ada
        $isNoConsultation = Consultation::with(['doctor', 'schedule'])
            ->where('patient_id', '=', $request->user()->id)
            ->whereHas('schedule', function (Builder $query) use ($curDate, $curTime) {
                $query->where('date', '>', $curDate);
                $query->orWhere(function ($query) use ($curDate, $curTime) {
                    $query->where('date', '=', $curDate);
                    $query->where('shift_end', '>', $curTime);
                });
            })
            ->where('is_done', '=', false)
            ->first();

        if (is_null($isNoConsultation)) { // Cek apakah terdapat data konsultasi

            // Jika tidak terdapat data konsultasi ambil data jadwal dan tampilkan halaman booking
            $schedules =  Schedule::where('is_active', '=', true)
                ->where(function ($query) use ($curDate, $curTime) {
                    $query->where('date', '>', $curDate);
                    $query->orWhere(function ($query) use ($curDate, $curTime) {
                        $query->where('date', '=', $curDate);
                        $query->where('shift_end', '>', $curTime);
                    });
                })->get();

            return view('pasien.booking', [
                'schedules' => $schedules,
            ]);
        } else {
            // Jika terdapat data konsultasi maka tampilkan data tersebut

            // Ambil data konsultasi yang sedang ditangani sekarang
            if ($isNoConsultation->date === $curDate && ($isNoConsultation->schedule->shift_start <= $curTime && $isNoConsultation->schedule->shift_end >= $curTime)) {
                $currentConsultation = Consultation::select('queue')
                    ->where('schedule_id', '=', $isNoConsultation->schedule_id)
                    ->where('is_done', '=', false)
                    ->orderBy('queue')->first();
            }

            return view('pasien.booking-done', [
                'consultation' => $isNoConsultation,
                'currentQueue' => $currentConsultation->queue ?? 'Jadwal konsultasi anda belum dimulai.',
            ]);
        }
    }

    /**
     * Action untuk menangani proses booking
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function newBooking(Request $request)
    {
        // Definisikan aturan otorisasi
        Gate::authorize('patient-page');
        Gate::authorize('no-consultation');

        // Validasi data yang diberikan
        $consultationData = $request->validate([
            'schedule' => [
                'required',
                'alpha_num',
                'exists:schedules,id',
                Rule::notIn('Pilih hari/tanggal konsultasi'),
            ]
        ]);

        // Ambil data jadwal yang dipilih
        $schedule = Schedule::with('consultations')->find($consultationData['schedule']);

        // Jika jadwal tidak aktif maka pasien tidak bisa
        // booking jadwal tersebut
        if (!$schedule->is_active) {
            return back()->withErrors([
                'bookFail' => 'Jadwal yang anda booking tidak aktif.',
            ]);
        }

        // Ambil nomor antrian terakhir
        $lastQueue = $schedule->consultations
            ->sortByDesc('queue')
            ->values()->all();

        if (count($lastQueue) === 0) { // Jika tidak ada konsultasi maka set nomor antrian jadi 1
            $lastQueue = 1;
        } else {
            $lastQueue = $lastQueue[0]->queue + 1; // Jika ada maka nomor antrian adalah atrian terakhir + 1
        }

        // Buat instance model konsultasi baru
        $consultation = new Consultation();

        // Tambahkan data konsultasi
        $consultation->patient_id = $request->user()->id;
        $consultation->doctor_id = 1;
        $consultation->schedule_id = $consultationData['schedule'];
        $consultation->date = $schedule->date;
        $consultation->queue = $lastQueue;

        // Simpan konsultasi
        if ($consultation->save()) {
            $request->session()->flash('success', true);

            return redirect(route('pasien.booking'));
        } else {
            return back()->withErrors([
                'bookFail' => 'Terjadi kesalahan ketika melakukan booking.',
            ]);
        }
    }
}