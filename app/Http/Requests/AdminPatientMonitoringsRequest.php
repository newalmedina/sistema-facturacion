<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminPatientMonitoringsRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!auth()->user()->isAbleTo('admin-patients-monitoring-create') && !auth()->user()->isAbleTo('admin-patients-monitoring-update')) {
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
            'date' => 'required',
           // 'hideDescription' => 'required',

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
            'date.required' => trans('patient-monitorings/admin_lang.fields.date_required'),
            // 'hideDescription.required' => trans('patient-monitorings/admin_lang.fields.description_required'),

        ];
    }
}
