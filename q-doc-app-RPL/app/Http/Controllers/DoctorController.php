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
    /**
     * Action untuk menampilkan tabel jadwal
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showSchedules(Request $request)
    {
        // Definisikan aturan otorisasi
        Gate::authorize('doctor-page');

        // Ambil tanggal dan jam sekarang
        $curDate = now('Asia/Jakarta')->toDateString();
        $curTime = now('Asia/Jakarta')->toTimeString();

        // Dapatkan jumlah data jadwal yang sudah berlalu
        $pastScheduleCount = $request->user()->schedules()->where('date', '<', $curDate)
            ->orWhere(function ($query) use ($curDate, $curTime) {
                $query->where('date', '=', $curDate);
                $query->where('shift_end', '<', $curTime);
            })->count();

        $schedules =  $request->user()
            ->schedules()
            ->orderBy('date')
            ->orderBy('shift_start')
            ->where('date', '>', $curDate)
            ->orWhere(function ($query) use ($curDate, $curTime) {
                $query->where('date', '=', $curDate);
                $query->where('shift_end', '>', $curTime);
            })->get();

        return view('dokter.jadwal', [
            'schedules' => $schedules,
            'pastScheduleCount' => $pastScheduleCount,
        ]);
    }

    /**
     * Action untuk menangani proses tambah jadwal
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function newSchedule(Request $request)
    {
        // Definisikan aturan otorisasi
        Gate::authorize('doctor-page');

        // Dapatkan data jadwal yang diinput

        $scheduleData = $request->all();

        // Pastikan format waktu shift sudah benar (H:i:s)

        if (count(explode(':', $scheduleData['shift_start'])) === 2) {
            $scheduleData['shift_start'] .= ':00';
        }

        if (count(explode(':', $scheduleData['shift_end'])) === 2) {
            $scheduleData['shift_end'] .= ':00';
        }

        // Dapatkan jadwal sebelum dan sesudah jadwal yang akan diinput
        // Data ini digunakan ketika validasi untuk mencegah terjadinya jadwal yang bertabrakan

        $lowerSchedule = Schedule::where([
            ['user_id', '=', $request->user()->id],
            ['date', '=', $scheduleData['date']],
            ['shift_start', '<', $scheduleData['shift_start']],
        ])->orderBy('shift_start', 'desc')->first();

        $greaterSchedule = Schedule::where([
            ['user_id', '=', $request->user()->id],
            ['date', '=', $scheduleData['date']],
            ['shift_end', '>', $scheduleData['shift_end']],
        ])->orderBy('shift_start')->first();

        // Mulai validasi

        $validator = Validator::make($scheduleData, [
            'date' => ['required', 'date_format:Y-m-d'],
            'shift_start' => ['required', 'date_format:H:i:s', 'before:shift_end', ($lowerSchedule ? 'after:' . $lowerSchedule->shift_end : '')],
            'shift_end' => ['required', 'date_format:H:i:s', 'after:shift_start', ($greaterSchedule ? 'before:' . $greaterSchedule->shift_start : '')],
        ]);

        $validator->sometimes('shift_end', ['after:' . now()->toTimeString()], function ($input) {
            return $input->date === now()->toDateString();
        });

        $validatedData = $validator->validate();

        // Cek apakah jadwal yang diinput sudah ada di database

        $isExist = Schedule::where([
            ['user_id', '=', $request->user()->id],
            ['date', '=', $validatedData['date']],
            ['shift_start', '=', $validatedData['shift_start']],
            ['shift_end', '=', $validatedData['shift_end']],
        ])->get();

        if ($isExist->count() !== 0) {
            return back()->withErrors([
                'saveFail' => 'Jadwal yang akan anda tambahkan sudah ada di database.',
            ]);
        }

        // Data jadwal selesai di validasi
        // Simpan data jadwal ke dalam database

        $newSchedule = new Schedule($validatedData);

        $newSchedule->user_id = $request->user()->id;

        if ($newSchedule->save()) {
            $request->session()->flash('success', true);

            return back();
        } else {
            return back()->withErrors([
                'saveFail' => 'Terjadi kesalahan ketika menyimpan data jadwal.',
            ]);
        }
    }

    /**
     * Action untuk menangani proses tambah jadwal mingguan
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addWeeklySchedule(Request $request)
    {
        // Definisikan aturan otorisasi
        Gate::authorize('doctor-page');

        // Dapatkan data tanggal sekarang
        $date = \Carbon\Carbon::now('Asia/Jakarta');
        $dateString = $date->toDateString();
        $timeString = $date->toTimeString();
        $dateTimeString = $date->toDateTimeString();

        // Cek apakah masih terdapat jadwal yang bisa ditambahkan
        // Kondisi ketika hari jumat sudah terlewat dan shift terakhir sudah selesai
        if ($date->dayOfWeekIso > 5 || ($date->dayOfWeekIso === 5 && $timeString > '22:00:00')) {
            $date->next(\Carbon\Carbon::MONDAY);
            $dateString = $date->toDateString();
            $timeString = $date->toTimeString();
        }

        // Buat wadah data jadwal
        $bulkData = [];

        // Tentukan berapa hari yang perlu ditambahkan jadwal
        $neededDay = (7 - $date->dayOfWeekIso) - 1;

        // Jam shift
        $morningShift = ['07:00:00', '09:00:00'];
        $nightShift = ['20:00:00', '22:00:00'];

        // Array untuk menampung data untuk validasi
        $scheduleInfoForValidation = [];

        // Mulai pembuatan data jadwal
        for ($i = 0; $i < $neededDay; $i++) {

            // Untuk hari pertama lakukan pengecekan shift
            if ($i === 0) {
                // Tentukan apakah kita perlu menambahkan shift pertama
                if ($timeString < '09:00:00') {

                    // Tambahkan shift pagi
                    $bulkData[] = [
                        'user_id' => 1,
                        'date' => $dateString,
                        'shift_start' => ($timeString < '07:00:00' ? $morningShift[0] : $timeString),
                        'shift_end' => $morningShift[1],
                        'is_active' => true,
                        'created_at' => $dateTimeString,
                        'updated_at' => $dateTimeString
                    ];

                    $scheduleInfoForValidation[] = [
                        'date' => $dateString,
                        'shift_start' => ($timeString < '07:00:00' ? $morningShift[0] : $timeString),
                        'shift_end' => $morningShift[1],
                    ];

                    // Tambahkan shift malam
                    $bulkData[] = [
                        'user_id' => 1,
                        'date' => $dateString,
                        'shift_start' => $nightShift[0],
                        'shift_end' => $nightShift[1],
                        'is_active' => true,
                        'created_at' => $dateTimeString,
                        'updated_at' => $dateTimeString
                    ];

                    $scheduleInfoForValidation[] = [
                        'date' => $dateString,
                        'shift_start' => $nightShift[0],
                        'shift_end' => $nightShift[1],
                    ];
                } else if ($timeString >= '09:00:00' && $timeString < '22:00:00') { // Kondisi shift pagi sudah lewat
                    // Tambahkan shift malam
                    $bulkData[] = [
                        'user_id' => 1,
                        'date' => $dateString,
                        'shift_start' => ($timeString < '20:00:00' ? $nightShift[0] : $timeString),
                        'shift_end' => $nightShift[1],
                        'is_active' => true,
                        'created_at' => $dateTimeString,
                        'updated_at' => $dateTimeString
                    ];

                    $scheduleInfoForValidation[] = [
                        'date' => $dateString,
                        'shift_start' => ($timeString < '20:00:00' ? $nightShift[0] : $timeString),
                        'shift_end' => $nightShift[1],
                    ];
                }
            } else {
                // Override variabel string tanggal
                $dateString = $date->toDateString();

                // Tambahkan shift pagi
                $bulkData[] = [
                    'user_id' => 1,
                    'date' => $dateString,
                    'shift_start' => $morningShift[0],
                    'shift_end' => $morningShift[1],
                    'is_active' => true,
                    'created_at' => $dateTimeString,
                    'updated_at' => $dateTimeString
                ];

                $scheduleInfoForValidation[] = [
                    'date' => $dateString,
                    'shift_start' => $morningShift[0],
                    'shift_end' => $morningShift[1],
                ];

                // Tambahkan shift malam
                $bulkData[] = [
                    'user_id' => 1,
                    'date' => $dateString,
                    'shift_start' => $nightShift[0],
                    'shift_end' => $nightShift[1],
                    'is_active' => true,
                    'created_at' => $dateTimeString,
                    'updated_at' => $dateTimeString
                ];

                $scheduleInfoForValidation[] = [
                    'date' => $dateString,
                    'shift_start' => $nightShift[0],
                    'shift_end' => $nightShift[1],
                ];
            }

            // increment variabel tanggal
            $date->next('24:00');
        }

        // Lakukan validasi apakah terdapat jadwal duplikat
        $query = Schedule::query();
        $validationCount = 0;

        foreach ($scheduleInfoForValidation as $info) {
            if ($validationCount === 0) {
                $query->where(function (\Illuminate\Database\Eloquent\Builder $query) use ($info) {
                    $query->where([
                        ['date', '=', $info['date']],
                        ['shift_start', '=', $info['shift_start']],
                        ['shift_end', '=', $info['shift_end']],
                    ]);
                });

                $validationCount++;
            } else {
                $query->orWhere(function (\Illuminate\Database\Eloquent\Builder $query) use ($info) {
                    $query->where([
                        ['date', '=', $info['date']],
                        ['shift_start', '=', $info['shift_start']],
                        ['shift_end', '=', $info['shift_end']],
                    ]);
                });
            }
        }

        if ($query->count() > 0) { // Jika terdapat jadwal duplikat berikan pesar error
            return back()->withErrors([
                'weeklyScheduleFail' => 'Terdapat duplikasi terhadap jadwal yang akan ditambahkan.'
            ]);
        }

        // Simpan data ke database
        if (Schedule::insert($bulkData)) {
            $request->session()->flash('success', true);

            return back();
        } else {
            return back()->withErrors([
                'weeklyScheduleFail' => 'Terjadi kesalahan di latar belakang. Mohon coba lagi.'
            ]);
        }
    }

    /**
     * Action untuk menangani proses toggle status aktif jadwal
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Schedule $schedule
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function toggleStatus(Request $request, Schedule $schedule)
    {
        // Definisikan aturan otorisasi
        Gate::authorize('doctor-page');
        Gate::authorize('update-schedule', $schedule);

        // Toggle status aktif
        $schedule->is_active = !$schedule->is_active;

        // Simpan data jadwal
        if ($schedule->save()) {
            $request->session()->flash('toggleStateSuccess', true);
            $request->session()->flash('toggleStateMessage', 'Berhasil ' . ($schedule->is_active ? 'mengkatifkan' : 'menonaktifkan') . ' jadwal.');

            return response(
                json_encode([
                    'success' => true,
                    'message' => 'Berhasil ' . ($schedule->is_active ? 'mengkatifkan' : 'menonaktifkan') . ' jadwal.',
                ]),
                200
            );
        } else {
            return response(
                json_encode([
                    'success' => false,
                    'message' => 'Terjadi kesalahan di latar belakang, mohon coba lagi.',
                ]),
                500
            );
        }

        return response()->json($schedule);
    }

    /**
     * Action untuk menangani proses hapus jadwal
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Schedule $schedule
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function deleteSchedule(Request $request, Schedule $schedule)
    {
        // Definisikan aturan otorisasi
        Gate::authorize('doctor-page');
        Gate::authorize('delete-schedule', $schedule);

        // Jika jadwal tidak memiliki konsultasi yang belum selesai maka
        // lanjutkan proses penghapusan jadwal
        if ($schedule->delete()) {
            $request->session()->flash('deleteSuccess', true);

            return response(
                json_encode([
                    'success' => true,
                    'message' => 'Berhasil menghapus jadwal.',
                ]),
                200
            );
        } else {
            return response(
                json_encode([
                    'success' => false,
                    'message' => 'Terjadi kesalahan di latar belakang, mohon coba lagi.',
                ]),
                500
            );
        }
    }

    /**
     * Action untuk menangani proses hapus jadwal lama
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function deletePastSchedules(Request $request)
    {
        // Definisikan aturan otorisasi
        Gate::authorize('doctor-page');

        // Dapatkan tanggal dan jam sekarang
        $curDate = now('Asia/Jakarta')->toDateString();
        $curTime = now('Asia/Jakarta')->toTimeString();

        // Dapatkan data jadwal yang sudah berlalu
        $pastSchedules = $request->user()->schedules()->where('date', '<', $curDate)
            ->orWhere(function ($query) use ($curDate, $curTime) {
                $query->where('date', '=', $curDate);
                $query->where('shift_end', '<', $curTime);
            })->get();

        // Hapus jadwal dan flash pesan berdasarkan status keberhasilan
        if (Schedule::destroy($pastSchedules->modelKeys()) !== 0) {
            $request->session()->flash('deleteSuccess', true);
        } else {
            $request->session()->flash('deleteFailed', true);
        }

        return redirect(route('dokter.jadwal.show'));
    }
}