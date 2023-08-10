<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalSpecialization extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name',  'active', 'description'];
    protected $table = "medical_specializations";

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
