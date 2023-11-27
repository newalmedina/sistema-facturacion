<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientMedicine extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "patient_medicines";

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', "id");
    }
    public function center()
    {
        return $this->belongsTo('App\Models\Center', 'center_id', "id");
    }

    public function details()
    {
        return $this->hasMany('App\Models\PatientMedicineDetail', 'patient_medicine_id', "id");
    }
    public function getdateFormattedAttribute()
    {
        if (!empty($this->date)) {
            return (Carbon::createFromFormat('Y-m-d', $this->date))->format('d/m/Y');
        }

        return '';
    }
}
