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
    public function soySuDoctor()
    {
        if ($this->doctor_id == Auth::user()->id) {
            return true;
        }
        return false;
    }
}
