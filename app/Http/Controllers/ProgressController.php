<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Progress;

class ProgressController extends Controller
{
    public static function store_progress(Request $request)
    {
        // Validamos los datos enviados
        $validated = $request->validate([
            'course_id' => 'required|integer',
            'user_id' => 'required|integer',
            'lesson_id' => 'required|integer',
            'percentage' => 'required|integer',
            'advanced_current_time' => 'required',
            'total_video_time' => 'required'
        ]);

        $consult = Progress::where('course_id', $request->input("course_id"))
                            ->where('user_id', $request->input("user_id"))
                            ->where('lesson_id', $request->input("lesson_id"))->first();

        if (empty($consult)){
            self::store($request);
        }
        else{
            self::update($request);
        } 
    }

    public static function store(Request $request)
    {

    }

    public static function update()
    {

    }
}
