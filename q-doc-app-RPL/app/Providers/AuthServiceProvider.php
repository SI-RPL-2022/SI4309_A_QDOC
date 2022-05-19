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
    }

}