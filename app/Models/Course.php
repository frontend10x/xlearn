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
}
