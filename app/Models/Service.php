<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name',  'active'];

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
    public function insuranceCarriers()
    {
        return $this->belongsToMany(InsuranceCarrier::class, 'service_insurance_carriers', 'service_id', 'insurance_carrier_id')->withPivot(['price']);
    }
}
