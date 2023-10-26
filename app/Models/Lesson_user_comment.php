<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Lesson_user_comment extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function replies()
    {
        return $this->hasMany(Lesson_user_comment::class, 'parent_comment_id', 'comId');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

}