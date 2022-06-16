<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Consultation;
use App\Models\Schedule;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
                // Otorisasi untuk halaman pasien
                Gate::define('patient-page', function (User $user) {
                    return !$user->is_doctor;
                });
        
                // Otorisasi untuk halaman  dokter
                Gate::define('doctor-page', function (User $user) {
                    return $user->is_doctor;
                });
                
                // Otorisasi untuk izin mengedit profil
        Gate::define('update-profile', function (User $user, $id) {
            return $user->id === $id;
        });
        // Otorisasi untuk izin mengedit jadwal
        Gate::define('update-schedule', function (User $user, Schedule $schedule) {
            return $user->id === $schedule->user_id;
        });

        // Otorisasi untuk izin menghapus jadwal
        Gate::define('delete-schedule', function (User $user, Schedule $schedule) {
            return $user->id === $schedule->user_id;
        });
                // Otorisasi berdasarkan keberadaan data konsultasi
                Gate::define('no-consultation', function (User $user) {
                    $curDate = now('Asia/Jakarta')->toDateString();
                    $curTime = now('Asia/Jakarta')->toTimeString();
        
                    $notDoneConsult = Consultation::where('patient_id', '=', $user->id)
                        ->whereHas('schedule', function (Builder $query) use ($curDate, $curTime) {
                            $query->where('date', '>', $curDate);
                            $query->orWhere(function ($query) use ($curDate, $curTime) {
                                $query->where('date', '=', $curDate);
                                $query->where('shift_end', '>', $curTime);
                            });
                        })
                        ->where('is_done', '=', false)->first();
        
                    return is_null($notDoneConsult);
                });

                // Otorisasi untuk izin menambah resep dokter
                Gate::define('add-receipt', function (User $user, Consultation $consultation) {
                    return $user->id === $consultation->doctor->id;
        });
    }

}