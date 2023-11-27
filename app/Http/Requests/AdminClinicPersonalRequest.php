<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminClinicPersonalRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!auth()->user()->isAbleTo('admin-clinic-personal-update')) {
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
        return [
            //'doctor_profile.exequatur' => 'required',

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
            'doctor_profile.exequatur.required' => trans('clinic-personal/admin_lang.fields.exequatur_required'),


        ];
    }
}
