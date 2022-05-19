<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {

            // Primary ke
            $table->id();

            // Foreign key tabel user
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Kolom tanggal jadwal
            $table->date('date');

            // Kolom jam mulai jadwal
            $table->time('shift_start');

            // Kolom jam selesai jadwal
            $table->time('shift_end');

            // Kolom status jadwal
            $table->boolean('is_active')
                ->default(true);

            // Timestamp
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
        Schema::dropIfExists('schedules');
    }
}
