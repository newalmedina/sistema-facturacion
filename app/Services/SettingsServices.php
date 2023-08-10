<?php

/**
 * Created by PhpStorm.
 * User: toni
 * Date: 28/10/2015
 * Time: 10:03
 */

namespace App\Services;

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
        return $setting = (object)$data;
    }
}
