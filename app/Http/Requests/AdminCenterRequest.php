<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminCenterRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!auth()->user()->isAbleTo('admin-centers-create') && !auth()->user()->isAbleTo('admin-centers-update')) {
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
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'province_id' => 'required',
            'municipio_id' => 'required',
            'address' => 'required',
            'image' => 'nullable|image',
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
            'email.required' => trans('centers/admin_lang.fields.email_required'),
            'email.email' => trans('centers/admin_lang.fields.email_invalid_format'),
            'province_id.required' => trans('centers/admin_lang.fields.province_id_required'),
            'municipio_id.required' => trans('centers/admin_lang.fields.municipio_id_required'),
            'address.required' => trans('centers/admin_lang.fields.address_required'),
            'image.image' => trans('centers/admin_lang.fields.image_format'),
            'image.mimes' => trans('centers/admin_lang.fields.image_mimes'),

        ];
    }
}
