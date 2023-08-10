<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Center extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "centers";

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
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_centers', 'center_id', 'user_id');
    }
}
