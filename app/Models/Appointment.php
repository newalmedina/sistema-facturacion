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

    public function getState()
    {
        switch ($this->state) {
            case 'pendiente':
            $state="Pendiente";
            break;
            case 'facturado':
                $state="Facturado";
                break;
                case 'finalizado':
                    $state="Finalizado";
                    break;
                    
                    default:
                    $state="Sin estado";
            
            break;
        }
        return $state;
    }
    public function getStateColor()
    {
        switch ($this->state) {
            case 'pendiente':
            $color="#6c757d";
            break;
            case 'facturado':
                $color="#ffc107";
                break;
                case 'finalizado':
                    $color="#28a745";
                    break;
                    
                    default:
                    $color="Sin estado";
            
            break;
        }
        return $color;
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
            !auth()->user()->isAbleTo('admin-appointments-update-created-by-user')
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


    public function scopeSelectedCenter($query)
    {
      return   $query->where("appointments.center_id", auth()->user()->userProfile->center->id);
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

                        $query->where("appointments.doctor_id",  Auth::user()->id);
                    }
                    if (auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-created-by-user')) {

                        $query->orWhere("appointments.created_by", Auth::user()->id);
                    }
                });
            }
        }
        return $query;
    }

    public function scopeCanList($query)
    {
       
       
        if (
            !auth()->user()->isAbleTo('admin-appointments-list-all') &&
            !auth()->user()->isAbleTo('admin-appointments-list-doctor')      &&
            !auth()->user()->isAbleTo('admin-appointments-list-created-by-user')
        ) {
            //si no tiene ninguno de los permisos no muestre ningun registro 

            $query->whereNull("appointments.id");
        } else {
            if (!auth()->user()->isAbleTo('admin-appointments-list-all')) {
                $query->where(function ($query) {

                    if (auth()->user()->isAbleTo('admin-appointments-list-doctor')) {
                        $query->where("appointments.doctor_id",  Auth::user()->id);
                    }
                    if (auth()->user()->isAbleTo('admin-appointments-list-created-by-user')) {
                        $query->orWhere("appointments.created_by", Auth::user()->id);
                    }
                });
               
            }
        }
        
    }
    public function scopeCanEdit()
    {     

        if (
            !auth()->user()->isAbleTo('admin-appointments-update-all') &&
            !auth()->user()->isAbleTo('admin-appointments-update-doctor')      &&
            !auth()->user()->isAbleTo('admin-appointments-update-created-by-user')
        ) {
            //si no tiene ninguno de los permisos no muestre ningun registro 

            return false;
        } else {
            if (auth()->user()->isAbleTo('admin-appointments-update-all')) {
                return true;
               
            } else {
                if (auth()->user()->isAbleTo('admin-appointments-update-doctor')) {
                    return $this->soySuDoctor();
                }
                if (auth()->user()->isAbleTo('admin-appointments-update-created-by-user')) {
                    return $this->soySuCreador();
                }
            }
        }
        return false;
    }

    public function scopeCanShow()
    {     
        if (
            !auth()->user()->isAbleTo('admin-appointments-read-all') &&
            !auth()->user()->isAbleTo('admin-appointments-read-doctor')      &&
            !auth()->user()->isAbleTo('admin-appointments-read-created-by-user')
        ) {
            //si no tiene ninguno de los permisos no muestre ningun registro 

            return false;
        } else {
            if (auth()->user()->isAbleTo('admin-appointments-read-all')) {
                return true;
                
            } else {
                if (auth()->user()->isAbleTo('admin-appointments-read-doctor')) {
                    return $this->soySuDoctor();
                }
                if (auth()->user()->isAbleTo('admin-appointments-read-created-by-user')) {
                    return $this->soySuCreador();
                }
            }
        }
        return false;
    }
   
    public function scopeCanFacturar()
    {
        //si no se ha facturado no se puede finalizar

        if ( $this->state=="facturado" || $this->state=="finalizado") {
            return false;
        }

        if (
            !auth()->user()->isAbleTo('admin-appointments-facturar-all') &&
            !auth()->user()->isAbleTo('admin-appointments-facturar-doctor')      &&
            !auth()->user()->isAbleTo('admin-appointments-facturar-created-by-user')
        ) {
            //si no tiene ninguno de los permisos no muestre ningun registro 
            return false;
        } else {
            if (!auth()->user()->isAbleTo('admin-appointments-facturar-all')) {
                if (auth()->user()->isAbleTo('admin-appointments-facturar-doctor') && auth()->user()->isAbleTo('admin-appointments-facturar-created-by-user')) {
                    if($this->soySuDoctor() || $this->soySuCreador()){                        
                        return true ;
                    }
                }

                if (auth()->user()->isAbleTo('admin-appointments-facturar-doctor')) {
                    return $this->soySuDoctor();
                }
                if (auth()->user()->isAbleTo('admin-appointments-facturar-created-by-user')) {
                    return $this->soySuCreador();
                }
            } else {
                return true;
            }
        }
        return false;
    }
    public function scopeCanFinalizar()
    {    
        //si no se ha facturado no se puede finalizar
        if ( $this->state=="pendiente" || $this->state=="finalizado") {
            return false;
        }
        if (
            !auth()->user()->isAbleTo('admin-appointments-end-all') &&
            !auth()->user()->isAbleTo('admin-appointments-end-doctor')      &&
            !auth()->user()->isAbleTo('admin-appointments-end-created-by-user')
        ) {
            //si no tiene ninguno de los permisos no muestre ningun registro 
            return false;
        } else {
            if (!auth()->user()->isAbleTo('admin-appointments-end-all')) {
                if (auth()->user()->isAbleTo('admin-appointments-end-doctor') && auth()->user()->isAbleTo('admin-appointments-end-created-by-user')) {
                    if($this->soySuDoctor() || $this->soySuCreador()){                        
                        return true ;
                    }
                }

                if (auth()->user()->isAbleTo('admin-appointments-end-doctor')) {
                    return $this->soySuDoctor();
                }
                if (auth()->user()->isAbleTo('admin-appointments-end-created-by-user')) {
                    return $this->soySuCreador();
                }
            } else {
                return true;
            }
        }
        return false;
    }

    public function scopeCanDelete()
    {
        if (
            !auth()->user()->isAbleTo('admin-appointments-delete-all') &&
            !auth()->user()->isAbleTo('admin-appointments-delete-doctor')      &&
            !auth()->user()->isAbleTo('admin-appointments-delete-created-by-user')
        ) {
            //si no tiene ninguno de los permisos no muestre ningun registro 
            return false;
        } else {
            if($this->state =="facturado" && !auth()->user()->isAbleTo('admin-appointments-delete-facturar')){
                return false;
            }
            if($this->state =="finalizado" && !auth()->user()->isAbleTo('admin-appointments-delete-end')){
                return false;
            }
            
            if (!auth()->user()->isAbleTo('admin-appointments-delete-all')) {
                if (auth()->user()->isAbleTo('admin-appointments-delete-doctor') && auth()->user()->isAbleTo('admin-appointments-delete-created-by-user')) {
                    if($this->soySuDoctor() || $this->soySuCreador()){                        
                        return true ;
                    }
                }

                if (auth()->user()->isAbleTo('admin-appointments-delete-doctor')) {
                    return $this->soySuDoctor();
                }
                if (auth()->user()->isAbleTo('admin-appointments-delete-created-by-user')) {
                    return $this->soySuCreador();
                }
            } else {
                return true;
            }
        }
        return false;
    }
  
}
