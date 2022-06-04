<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sub_companies extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function groups(){
        return $this->belongsToMany(groups::class,"subcompanie_group","subcompanie_id","group_id");
    }
}
