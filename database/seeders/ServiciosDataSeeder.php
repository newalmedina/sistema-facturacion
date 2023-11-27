<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ServiciosDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $data = [
            [
                "name" => "Consulta General",
                "active" => 1,
                "price" => 2500,
                "description" => "Esta es una consulta general",
            ],
            [
                "name" => "Consulta General",
                "active" => 1,
                "price" => 0,
                "description" => "Esta es una consulta general",
            ],
        ];

        foreach ($data as $key => $value) {

            $service = new Service();

            $service->name = $value['name'];
            $service->active = $value['active'];
            $service->price = $value['price'];
            $service->description = $value['description'];
            $service->save();
        }
    }
}
