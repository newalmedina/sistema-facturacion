<?php

namespace Database\Seeders;

use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatosPruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\DoctorProfile::factory(5)->create();
        \App\Models\PatientProfile::factory(10)->create();
        // \App\Models\PatientMedicalStudies::factory(100)->create();
        // \App\Models\PatientMonitoring::factory(400)->create();
        // \App\Models\Appointment::factory(1000)->create();
    }
}
