<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $doctor = new User();

        $doctor->name = 'Maibillisa';
        $doctor->phone_number = '+62 823-8783-0088';
        $doctor->birth_place = 'Tembilahan';
        $doctor->birth_date = '1991-03-31';
        $doctor->gender = 'Perempuan';
        $doctor->is_doctor = true;
        $doctor->sip = '503/DPMTSP-SIP-DOKTER/68';
        $doctor->password = 'maibill123';

        $doctor->save();
    }
}