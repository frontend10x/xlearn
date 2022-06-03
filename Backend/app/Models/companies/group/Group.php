<?php

namespace App\Models\companies\group;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpParser\Builder\Class_;

class Group extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = [];

    public function users(){
        return $this->belongsToMany(User::class,"user_group","group_id","user_id");
    }
}
