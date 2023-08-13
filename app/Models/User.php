<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;

class User extends Authenticatable implements MustVerifyEmail
{
    use LaratrustUserTrait;
    use HasApiTokens, HasFactory, Notifiable;

    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userProfile()
    {
        return $this->hasOne('App\Models\UserProfile', 'user_id');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function doctorProfile()
    {
        return $this->hasOne('App\Models\DoctorProfile', 'user_id');
    }

    public function hasSelectedCenter()
    {
        if (!empty($this->userProfile)) {
            return $this->userProfile->selected_center;
        }
        return null;
    }

    public function centers()
    {
        return $this->belongsToMany(Center::class, 'user_centers', 'user_id', 'center_id')->withTimestamps();
    }

    public function specializations()
    {
        return $this->belongsToMany(MedicalSpecialization::class, 'doctor_specializations', 'user_id', 'specialization_id')->withTimestamps();
    }

    public function getCreatedAtFormattedAttribute()
    {
        try {
            if (!empty($this->created_at)) {
                return $this->created_at->format('d/m/Y');
            }
        } catch (\Exception $ex) {
        }

        return "";
    }

    public function scopeClinicPersonal($query)
    {

        return $query->join("role_user", "users.id", "=", "role_user.user_id")
            ->join("roles", "role_user.role_id", "=", "roles.id")
            ->whereIn("roles.name", ["doctor"]);
    }

    public function isDoctor()
    {

        $roles = $this->roles->where('name', "doctor");
        return  count($roles);
    }


    public function scopeClinicPersonalSelectedCenter($query)
    {
        return $query->join("user_centers", "users.id", "=", "user_centers.user_id")
            ->where("user_centers.center_id", Auth::user()->hasSelectedCenter());
    }
}
