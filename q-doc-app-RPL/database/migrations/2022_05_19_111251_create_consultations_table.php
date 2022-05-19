<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consultations', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key tabel user untuk data pasien
            $table->foreignId('patient_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Foreign key tabel user untuk data dokter
            $table->foreignId('doctor_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Foreign key tabel jadwal
            $table->foreignId('schedule_id')
                ->nullable()
                ->constrained('schedules')
                ->nullOnDelete();

            // Kolom tanggal jadwal
            $table->date('date');

            // Kolom antrian
            $table->unsignedSmallInteger('queue');

            // Kolom status konsultasi selesai (default=false)
            $table->boolean('is_done')->default(false);

            // Kolom resep (default=null)
            $table->longText('receipt')
                ->nullable()
                ->default(null);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consultations');
    }
}
