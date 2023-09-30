<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminCalendarController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isAbleTo('admin-appointments')) {
            app()->abort(403);
        }
        // dd(auth()->user()->isAbleTo("admin-appointments-delete"));

        $pageTitle =  trans('calendar/admin_lang.calendar');
        $title =  trans('calendar/admin_lang.calendar');

        return view(
            'calendar.admin_index',
            compact(
                'pageTitle',
                'title'
            )
        );
    }
}
