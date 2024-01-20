<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();

        // Obtenemos los datos enviados por el usuario

        $user->email = "nmedina@gmail.com";
        $user->password_changed_at =  Carbon::now();
        $user->password = Hash::make("Secret15*");

        $user->email_verified_at = Carbon::now();
        $user->active = true;

        // Guardamos el usuario
        $user->push();

        $userProfile = new UserProfile();

        $userProfile->first_name = "newal";
        $userProfile->last_name = "medina";
        $userProfile->address = "Fonollar";
        // $userProfile->province_id = 1;
        // $userProfile->municipio_id = 1;
        $userProfile->birthday = "1991-07-30";
        $userProfile->identification = "40221148360";
        $userProfile->phone = "00000000";
        $userProfile->gender = 'male';
        $userProfile->user_lang = 'es';


        $user->userProfile()->save($userProfile);


        DB::commit();
    }
}
