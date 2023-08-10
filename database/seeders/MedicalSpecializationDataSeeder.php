<?php

namespace Database\Seeders;

use App\Models\MedicalSpecialization;
use App\Models\Municipio;
use App\Models\Province;
use Illuminate\Database\Seeder;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MedicalSpecializationDataSeeder extends Seeder
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

            $jsonPath = public_path('json/especialidades.json');

            // Verificar si el archivo existe
            if (File::exists($jsonPath)) {
                $jsonData = File::get($jsonPath);

                // Convertir el JSON en un array
                $dataArray = json_decode($jsonData, true);

                // O si prefieres, convertirlo en un objeto
                $dataObject = json_decode($jsonData);

                foreach ($dataObject as $data) {
                    $specialization = new MedicalSpecialization();
                    $specialization->name = $data->name;
                    $specialization->active = 1;
                    $specialization->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // Manejar errores en caso de que la solicitud falle

        }
    }
}
