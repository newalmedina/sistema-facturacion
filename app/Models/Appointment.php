<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Appointment extends Model
{
    use HasFactory;
    use SoftDeletes;
    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', "id");
    }
    public function doctor()
    {
        return $this->belongsTo('App\Models\User', 'doctor_id', "id");
    }
    public function patient()
    {
        return $this->belongsTo('App\Models\User', 'user_id', "id");
    }
    public function service()
    {
        return $this->belongsTo('App\Models\Service', 'service_id', "id");
    }

    public function insurance()
    {
        return $this->belongsTo('App\Models\InsuranceCarrier', 'insurance_carrier_id', "id");
    }

    public function soySuDoctor()
    {
        if ($this->doctor_id == Auth::user()->id) {
            return true;
        }
        return false;
    }

    public function soySuCreador()
    {
        if ($this->created_by == Auth::user()->id) {
            return true;
        }
        return false;
    }

    public function scopeCanInfoBasicaDashboard()
    {

        if (
            !auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-info-basica-all') &&
            !auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-info-basica-doctor')      &&
            !auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-info-basica-created-by-user')
        ) {
            //si no tiene ninguno de los permisos no muestre ningun registro 

            return false;
        } else {
            if (!auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-info-basica-all')) {
                if (auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-info-basica-doctor')) {
                    return $this->soySuDoctor();
                }
                if (auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-info-basica-created-by-user')) {
                    return $this->soySuCreador();
                }
            } else {
                return true;
            }
        }
        return false;
    }
    public function scopeCanFacturarDashboard()
    {
        if (!empty($this->paid_at)) {
            return false;
        }

        if (
            !auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-facturar-all') &&
            !auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-facturar-doctor')      &&
            !auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-facturar-created-by-user')
        ) {
            //si no tiene ninguno de los permisos no muestre ningun registro 

            return false;
        } else {
            if (!auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-facturar-all')) {
                if (auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-facturar-doctor')) {
                    return $this->soySuDoctor();
                }
                if (auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-facturar-created-by-user')) {
                    return $this->soySuCreador();
                }
            } else {
                return true;
            }
        }
        return false;
    }
    public function scopeCanFinalizarDashboard()
    {
        //si no se ha facturado no se puede finalizar
        if (empty($this->paid_at) || !empty($this->finish_at)) {
            return false;
        }

        if (
            !auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-end-all') &&
            !auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-end-doctor')      &&
            !auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-end-created-by-user')
        ) {
            //si no tiene ninguno de los permisos no muestre ningun registro 
            return false;
        } else {
            if (!auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-end-all')) {
                if (auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-end-doctor')) {
                    return $this->soySuDoctor();
                }
                if (auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-end-created-by-user')) {
                    return $this->soySuCreador();
                }
            } else {
                return true;
            }
        }
        return false;
    }

    public function scopeCanShowDashboard($query)
    {
        if (
            !auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-all') &&
            !auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-doctor')      &&
            !auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-created-by-user')
        ) {
            //si no tiene ninguno de los permisos no muestre ningun registro 
            $query->whereNull("appointments.id");
        } else {

            if (!auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-all')) {
                $query->where(function ($query) {

                    if (auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-doctor')) {

                        $query->where("appointments.doctor_id", 1);
                    }
                    if (auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-created-by-user')) {

                        $query->orWhere("appointments.created_by", Auth::user()->id);
                    }
                });
            }
        }
        return $query;
    }
}
