<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function courses(){
        return $this->belongsto('App\Models\Course', 'course_id');
    }
}
