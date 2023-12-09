<?php

namespace Database\Seeders;

use App\Models\Center;
use App\Models\PatientMedicine;
use App\Models\PatientMedicineDetail;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker;

class PatientMedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        for ($i = 0; $i < 50; $i++) {
            $user = User::select("users.*")->inRandomOrder()->patients()->first();
            $doctor = User::inRandomOrder()->clinicPersonal()->active()->first();
            $center = Center::inRandomOrder()->first();

            $year = date('Y');

            // Generamos una fecha aleatoria dentro del año actual
            $fecha = $faker->dateTimeBetween($year . '-01-01', $year . '-12-31')->format('Y-m-d');

            $medicine = new PatientMedicine();
            $medicine->date = $fecha;
            $medicine->user_id = $user->id;
            $medicine->created_by = 1;
            $medicine->center_id = $center->id;
            $medicine->comment =  $faker->text($maxNbChars = 10);
            $medicine->save();

            for ($j = 0; $j < rand(1, 4); $j++) {
                $detail = new PatientMedicineDetail();
                $detail->patient_medicine_id = $medicine->id;
                $detail->medicine = $faker->text($maxNbChars = 10);
                $detail->dosis = $faker->text($maxNbChars = 10);
                $detail->frecuency = $faker->text($maxNbChars = 10);
                $detail->period = $faker->text($maxNbChars = 10);
                $detail->save();
            }
        }
    }
}
