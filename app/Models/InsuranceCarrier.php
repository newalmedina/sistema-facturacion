<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsuranceCarrier extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "insurance_carriers";

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id', 'id');
    }
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_insurance_carriers', 'insurance_carrier_id', 'service_id');
    }
}
