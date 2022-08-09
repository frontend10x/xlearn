<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

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

    public function coursesFavorites(){
        return $this->belongsToMany(Course::class,"user_course_favorite","user_id","course_id");
    }

    public function roles(){
        return $this->belongsto('App\Models\Roles', 'rol_id');
    }

    public function typeUser(){
        return $this->belongsto('App\Models\TypesUsers', 'type_id');
    }
    
    public function diagnostic(){
        return $this->hasMany('App\Models\Diagnostic', 'user_id');
    }
}
