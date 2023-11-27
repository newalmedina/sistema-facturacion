<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientMonitoring extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "patient_monitorings";

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

    // public function details()
    // {
    //     return $this->hasMany('App\Models\PatientMedicineDetail', 'patient_medicine_id', "id");
    // }

    public function diagnosis()
    {
        return $this->belongsToMany(Diagnosi::class, 'patient_monitorin_diagnosis', 'patient_monitoring_id', 'diagnosi_id')->withTimestamps();
    }

    public function getDiagnosisIdArrayFormattedAttribute()
    {
        $value=[];
        foreach ($this->diagnosis as $diagnosis) {
            $value[] =$diagnosis->id;
        }
        return $value;
    }
    public function getDiagnosisNameStringFormattedAttribute()
    {
        $value=[];
        foreach ($this->diagnosis as $diagnosis) {
            $value[] =$diagnosis->id;
        }
        
        return implode(', ',$value);
    }

    

    public function getdateFormattedAttribute()
    {
        if (!empty($this->date)) {
            return (Carbon::createFromFormat('Y-m-d', $this->date))->format('d/m/Y');
        }

        return '';
    }
}
