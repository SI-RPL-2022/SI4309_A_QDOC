<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    use HasFactory;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Cek jumlah konsultasi pada jadwal yang bersangkutan
        // dan nonaktifkan jadwal jika jumlah konsultasi
        // sudah mencapai 8 atu lebih
        self::saved(function ($consultation) {
            $consultationsCount = self::where('schedule_id', '=', $consultation->schedule_id)->count();

            if ($consultationsCount >= 8) {
                $schedule = $consultation->schedule;

                $schedule->is_active = false;

                $schedule->save();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'schedule_id',
        'date',
        'queue',
    ];

    /**
     * Get the pasien that owns the Consultation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the dokter that owns the Consultation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the schedule that owns the Consultation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}
