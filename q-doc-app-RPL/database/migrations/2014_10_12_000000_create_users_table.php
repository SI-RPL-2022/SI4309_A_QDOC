<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Kolom nama
            $table->string('name');

            // Kolom nomor hp
            $table->string('phone_number')->unique();

            // Kolom kota lahir
            $table->string('birth_place');

            // Kolom tanggal lahir
            $table->date('birth_date');

            // Kolom jenis kelamin
            $table->string('gender');

            // Kolom role dokter (default = false)
            $table->boolean('is_doctor')->default(false);

            // Kolom sip (default = null)
            $table->string('sip')->nullable()->default(null);

            // Kolom password
            $table->string('password');

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
        Schema::dropIfExists('users');
    }
}