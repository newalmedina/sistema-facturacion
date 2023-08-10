<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GeneralSettingSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $generalSettings = [
            "site_name",
            "image",
            "phone",
            "email",
            "address",
            "province_id",
            "municipio_id",
            "active",
        ];
        foreach ($generalSettings as  $value) {
            # code...
            $setting = new Setting();
            $setting->key = $value;
            $setting->value = ($value) == "active" ? 1 : "";
            $setting->group_slug = "general-settings";
            $setting->save();
        }
    }
}
