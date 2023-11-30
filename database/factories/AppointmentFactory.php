<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Center;
use App\Models\DoctorProfile;
use App\Models\PatientMedicalStudies;
use App\Models\PatientMonitoring;
use App\Models\PatientProfile;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {



        $user = User::select("users.*")->inRandomOrder()->patients()->active()->first();


        $center = Center::inRandomOrder()->first();
        $service = Service::inRandomOrder()->first();
        $fechaInicio = '0 years'; // Hace 30 años hacia atrás
        $fechaFin = '0 years'; // Hace 18 años hacia atrás

        $fecha = $this->faker->dateTimeThisYear();

        return [
            "title" => "Cita medica",
            "user_id" => $user->id,
            "center_id" => $center->id,
            "created_by" => 1,
            "doctor_id" => 1,
            "service_id" => $service->id,
            "start_at" => $fecha,
            "end_at" => $fecha,
            "price" => $service->price,
            "total" => $service->price,
            "color" => "#47a447",
            "comment" => $this->faker->paragraph(),
            "paid" => $this->faker->numberBetween(0, 1),
        ];
        // Otros campos de la tabla user_profiles

    }
}
