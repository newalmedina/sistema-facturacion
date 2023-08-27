<?php

/**
 * Created by PhpStorm.
 * User: toni
 * Date: 28/10/2015
 * Time: 10:03
 */

namespace App\Services;

use App\Models\Municipio;
use App\Models\Province;
use App\Models\Setting;

class SettingsServices
{
    public static function getGeneral()
    {
        $settings = Setting::general()->get();

        $data = [];
        foreach ($settings as $array) {
            $data[$array->key] = $array->value;
        }

        $data["complete_address"] = SettingsServices::getFullAddress($data);

        return $setting = (object)$data;
    }
    public static function getFullAddress($data)
    {
        $province = Province::find($data["province_id"]);
        $municipio = Municipio::find($data["municipio_id"]);

        $addressArray = [
            $data["address"],
            $province->name,
            $municipio->name
        ];

        return implode(", ", $addressArray);
    }
}
