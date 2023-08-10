<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    public function scopeGeneral($query)
    {
        return $query->where('group_slug', "general-settings");
    }
}
