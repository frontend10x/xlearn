<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson_user_comment;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Lesson_user_comment::all();
        return view('comments.index', compact('comments'));
    }
}