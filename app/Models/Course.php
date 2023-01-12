<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function areas(){
        return $this->belongsto('App\Models\Areas', 'area_id');
    }

    public function users(){
        return $this->belongsToMany(User::class,"user_course","course_id","user_id");
    }

    public function users_rel(){
        return $this->belongsToMany(User::class,'user_course')->withPivot('course_id');
    }
}
