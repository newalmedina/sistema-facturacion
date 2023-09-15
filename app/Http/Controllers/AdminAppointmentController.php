<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAppointmentController extends Controller
{
    public function index()
    {
        $pageTitle =  trans('appointments/admin_lang.appointments');
        $title =  trans('appointments/admin_lang.appointments');

        return view(
            'appointments.admin_index',
            compact(
                'pageTitle',
                'title'
            )
        );
    }
}
