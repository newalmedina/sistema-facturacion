<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminSettingRequest;
use App\Models\Municipio;
use App\Models\Province;
use App\Models\Setting;
use App\Services\SettingsServices;
use App\Services\StoragePathWork;

class AdminSettingsController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isAbleTo('admin-settings')) {
            app()->abort(403);
        }
        $disabledForm = false;
        if (!auth()->user()->isAbleTo('admin-settings-update')) {
            $disabledForm = true;
        }
        $setting = SettingsServices::getGeneral();

        $provincesList = Province::active()->get();
        $municipiosList = Municipio::active()->where("province_id", $setting->province_id)->get();

        $pageTitle = trans('settings/admin_lang.settings');
        $title = trans('settings/admin_lang.settings');

        return view('settings.admin_index', compact('pageTitle', 'title', 'setting', 'provincesList', 'municipiosList', 'disabledForm'));
    }
    public function update(AdminSettingRequest $request)
    {
        if (!auth()->user()->isAbleTo('admin-settings-update')) {
            app()->abort(403);
        }
        $data = $request->except(['_token', '_method', 'proengsoft_jsvalidation']);

        foreach ($data as $key => $value) {
            $setting = Setting::general()->where("key", $key)->first();
            if (!empty($setting)) {
                if ($key == "image") {
                    $image = $value;

                    if (!is_null($image)) {
                        $myServiceSPW = new StoragePathWork("settings");

                        if (!empty($setting->value)) {
                            $myServiceSPW->deleteFile($setting->value, '');
                            $setting->value = "";
                        }

                        $filename = $myServiceSPW->saveFile($image, '');
                        $setting->value = $filename;
                    }
                } else {
                    $setting->value = $value;
                }
                $setting->save();
            }
        }

        return redirect()->to('/admin/settings/')->with('success', trans('general/admin_lang.save_ok'));
    }

    public function getImage($image)
    {
        $myServiceSPW = new StoragePathWork("settings");
        return $myServiceSPW->showFile($image, '/settings');
    }
    public function deleteImage($image)
    {
        $myServiceSPW = new StoragePathWork("settings");
        $setting = Setting::general()->where("value", $image)->first();


        if (!empty($setting->value)) {
            $myServiceSPW->deleteFile($setting->value, '');
            $setting->value = "";
        }
        $setting->save();

        return response()->json(array(
            'success' => true,
            'msg' => trans("general/admin_lang.delete_ok"),
        ));
    }
}
