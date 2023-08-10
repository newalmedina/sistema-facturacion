<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminProfilePersonalInfoRequest;
use App\Http\Requests\AdminProfileRequest;
use App\Models\Municipio;
use App\Models\Province;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\StoragePathWork;
use Carbon\Carbon;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserProfileController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //Obtengo la información del usuario para pasarsela al formulario
        $user = User::with('userProfile')->find(auth()->user()->id);
        $tab = 'tab_1';
        $pageTitle =  trans('profile/admin_lang.my_profile');
        $title =  trans('profile/admin_lang.general_information');
        return view(
            'profile.admin_edit',
            compact(
                'pageTitle',
                'title',
                'user',
            )
        )->with('tab', $tab);
    }



    public function store(AdminProfileRequest $request)
    {

        // Id actual
        $idprofile = auth()->user()->id;

        // Creamos un nuevo objeto para nuestro nuevo usuario
        $user = User::with('userProfile')->find($idprofile);

        // Si el usuario no existe entonces lanzamos un error 404 :(
        if (is_null($user)) {
            app()->abort(404);
        }

        $myServiceSPW = new StoragePathWork("users");

        // Si la data es valida se la asignamos al usuario

        $user->email = $request->input('email');
        if (!empty($request->input('password'))) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->userProfile->first_name = $request->input('user_profile.first_name');
        $user->userProfile->last_name = $request->input('user_profile.last_name');

        if ($request->input("delete_photo") == '1') {
            $myServiceSPW->deleteFile($user->userProfile->photo, '');
            $user->userProfile->photo = "";
        }

        try {
            DB::beginTransaction();

            $file = $request->file('profile_image');

            if (!is_null($file)) {
                $myServiceSPW->deleteFile($user->userProfile->photo, '');

                $filename = $myServiceSPW->saveFile($file, '');

                $user->userProfile->photo = $filename;
            }
            $user->push();
            // Redirect to the new user page
            DB::commit();


            // Y Devolvemos una redirección a la acción show para mostrar el usuario
            return redirect('admin/profile')->with('success', trans('general/admin_lang.save_ok'));
        } catch (\PDOException $e) {
            // Woopsy
            dd($e);
            DB::rollBack();

            return redirect('profile'); // ->with('error-alert', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function personalInfo()
    {
        //Obtengo la información del usuario para pasarsela al formulario
        $user = User::with('userProfile')->find(auth()->user()->id);
        $tab = 'tab_2';
        $pageTitle =  trans('profile/admin_lang.my_profile');
        $title =  trans('profile/admin_lang.personal_information');

        $genders = [
            "male" => trans("general/admin_lang.male"),
            "female" => trans("general/admin_lang.female")
        ];

        $provincesList = Province::active()->get();
        $municipiosList = Municipio::active()->where("province_id", $user->userProfile->province_id)->get();

        return view(
            'profile.admin_edit_personal_info',
            compact(
                'pageTitle',
                'title',
                'user',
                'provincesList',
                'municipiosList',
                'genders'
            )
        )->with('tab', $tab);
    }

    public function updatePersonalInfo(AdminProfilePersonalInfoRequest $request)
    {

        // Id actual
        $idprofile = auth()->user()->id;
        // Creamos un nuevo objeto para nuestro nuevo usuario
        $user = User::with('userProfile')->find($idprofile);

        // Si el usuario no existe entonces lanzamos un error 404 :(
        if (is_null($user)) {
            app()->abort(404);
        }

        try {
            DB::beginTransaction();

            $user->userProfile->birthday = !empty($request->input('user_profile.birthday')) ? Carbon::createFromFormat("d/m/Y", $request->input('user_profile.birthday'))->format("Y-m-d") : null;
            $user->userProfile->identification = $request->input('user_profile.identification');
            $user->userProfile->phone = $request->input('user_profile.phone');
            $user->userProfile->mobile = $request->input('user_profile.mobile');
            $user->userProfile->gender = $request->input('user_profile.gender');
            $user->userProfile->province_id = $request->input('user_profile.province_id');
            $user->userProfile->municipio_id = $request->input('user_profile.municipio_id');
            $user->userProfile->address = $request->input('user_profile.address');

            $user->push();
            // Redirect to the new user page
            DB::commit();


            // Y Devolvemos una redirección a la acción show para mostrar el usuario
            return redirect('admin/profile/personal-info')->with('success', trans('general/admin_lang.save_ok'));
        } catch (\PDOException $e) {
            // Woopsy
            dd($e);
            DB::rollBack();

            return redirect('profile'); // ->with('error-alert', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function getPhoto($photo)
    {
        $myServiceSPW = new StoragePathWork("users");
        return $myServiceSPW->showFile($photo, '/users');
    }
    public function deleteImage($id)
    {
        $myServiceSPW = new StoragePathWork("users");

        $profile = UserProfile::where("user_id", $id)->first();

        if (!empty($profile->photo)) {
            $myServiceSPW->deleteFile($profile->photo, '');
            $profile->photo = "";
        }

        $profile->save();

        return response()->json(array(
            'success' => true,
            'msg' => trans("general/admin_lang.delete_ok"),
        ));
    }
}
