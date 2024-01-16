<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminPatientsRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!auth()->user()->isAbleTo('admin-patients-create') && !auth()->user()->isAbleTo('admin-patients-update')) {
            return false;
        }
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user_id = $this->route()->id ? $this->route()->id : null;
        return [
            'patient_profile.email' => 'required|email|unique:users,email,' . $user_id,
            // 'patient_profile.email' => 'required|email',
            //'active' => 'required',
            'user_profile.first_name' => 'required',
            'user_profile.last_name' => 'required',
            'profile_image' => 'nullable|image',
            //   'password' => 'nullable|same:password_confirm|min:8',
            'user_profile.birthday' => 'required',
            'user_profile.identification' => 'required',
            'user_profile.phone' => 'required',
            'user_profile.gender' => 'required',
            'user_profile.province_id' => 'required',
            'user_profile.municipio_id' => 'required',
            'user_profile.address' => 'required',
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_profile.first_name.required' => trans('patients/admin_lang.fields.first_name_required'),
            'user_profile.last_name.required' => trans('patients/admin_lang.fields.last_name_required'),
            'active.required' => trans('patients/admin_lang.fields.active_required'),
            'patient_profile.email.required' => trans('patients/admin_lang.fields.email_required'),
            'patient_profile.email.email' => trans('patients/admin_lang.fields.email_incorrect_format'),
            'patient_profile.email.unique' => trans('patients/admin_lang.fields.email_unique'),
            'password.required' => trans('patients/admin_lang.fields.password_required'),
            'password.same' => trans('patients/admin_lang.fields.password_confirmed'),
            'password.min' => trans('patients/admin_lang.fields.password_min'),
            'profile_image.image' => trans('patients/admin_lang.fields.photo_format'),
            'profile_image.mimes' => trans('patients/admin_lang.fields.photo_mimes'),
            'user_profile.birthday.required' => trans('patients/admin_lang.fields.birthday_required'),
            'user_profile.identification.required' => trans('patients/admin_lang.fields.identification_required'),
            'user_profile.phone.required' => trans('patients/admin_lang.fields.phone_required'),
            'user_profile.gender.required' => trans('patients/admin_lang.fields.gender_required'),
            'user_profile.province_id.required' => trans('patients/admin_lang.fields.province_id_required'),
            'user_profile.municipio_id.required' => trans('patients/admin_lang.fields.municipio_id_required'),
            'user_profile.address.required' => trans('patients/admin_lang.fields.address_required'),
        ];
    }
}
