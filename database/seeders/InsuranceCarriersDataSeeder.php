<?php

namespace Database\Seeders;

use App\Models\Diagnosi;
use App\Models\InsuranceCarrier;
use App\Models\MedicalSpecialization;
use App\Models\Municipio;
use App\Models\Province;
use Illuminate\Database\Seeder;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class InsuranceCarriersDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {

            $data = [
                "ARS Palic", "ARS Universal", "ARS Humano", "ARS Monumental", "ARS Simag", "ARS Renacer", "ARS Senasa", "ARS MetaSalud", "ARS Reservas", "ARS Futuro", "ARS Mapfre Salud", "ARS Asistencial GMM", "ARS Aneurisma", "ARS Atlántida", "ARS GCS Salud", "ARS Central Médico", "ARS APS", "ARS Estrella", "ARS Integral", "ARS Prima"
            ];

            foreach ($data as $value) {
                $specialization = new InsuranceCarrier();
                $specialization->name = ucfirst($value);
                $specialization->active = 1;
                $specialization->save();
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // Manejar errores en caso de que la solicitud falle

        }
    }
}
