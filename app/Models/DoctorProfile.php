<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorProfile extends Model
{
    use HasFactory;

    protected $table = "doctor_profiles";

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function specializations()
    {
        return $this->belongsToMany(MedicalSpecialization::class, 'doctor_specializations', 'user_id', 'specialization_id')->withTimestamps();
    }
}
