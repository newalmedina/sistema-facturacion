<?php

namespace App\Models;

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

    public function details()
    {
        return $this->belongsTo('App\Models\PatientMedicineDetail', 'patient_medicine_id', "id");
    }
}
