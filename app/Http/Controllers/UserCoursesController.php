<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diagnostic;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Mail\EmailNotification;
use Mail;

class UserCoursesController extends Controller
{
    public static function course_assignment($diagnostic_id)
    {
        try {

            if (empty($diagnostic_id))
                throw new Exception("No existe id de diagnostico para consultar");
            
            $consult = Diagnostic::where('confirmed', 1)->where('id', $diagnostic_id)->get()->first();

            $courses_ids = get_ids(json_decode($consult->answers, true), 'course');
            
            $users_ids = UserCoursesController::search_users($consult->group_id);

            if(empty($users_ids))
                throw new Exception("No existe el grupo con id: " . $consult->group_id);

            $relations = UserCoursesController::relate_courses_users($courses_ids, $users_ids);

            //Envio de correos a usuarios en caso de crear por lo menos una relación
            if($relations['amount_relationships'] > 0)
                $send_email = UserCoursesController::sending_emails_to_users($relations['related_ids']);

            return response('Se asignaron ' . $relations['amount_relationships'] . ' usuarios a cursos ');
        
        } catch (Exception $e) {
            return return_exceptions($e);
        }
    }

    //Buscamos y extraemos los ID de los usuarios segun el grupo dado
    public static function search_users($group_id)
    {
        try {
            
            $users = DB::table('user_group')
                        ->where('group_id', $group_id)
                        ->get('user_id');
            
            $users_ids = get_ids(json_decode($users, true), 'user_id');


            return $users_ids;

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    //Creamos las relaciones INEXISTENTES de usuarios y cursos en la tabla pivot (user_course)
    public static function relate_courses_users($courses_ids, $users_ids)
    {
        try {

            $stored_relationships = 0;
            $related_ids = [];
            
            foreach ($courses_ids as $value) {

                foreach ($users_ids as $item) {
                    
                    $find_duplicates = DB::table('user_course')
                                     ->where('course_id', $value)
                                     ->where('user_id', $item)
                                     ->first();
                
                    if(empty($find_duplicates)){
                        $courses = Course::find($value);
                        $courses->users()->attach($item);

                        $stored_relationships++;
                        array_push($related_ids, $item);
                    }

                }
            }

            return ['amount_relationships' => $stored_relationships, 'related_ids' => $related_ids];

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
        
    }

    //Enviamos los correos electronicos en caso de que se realice por lo menos una asignacion en la tabla pivot (user_course)
    public static function sending_emails_to_users( $array_ids )
    {
        try {

            $array_emails = [];

            $emails = User::whereIn('id', $array_ids)->get('email');

            foreach ($emails as $key => $value) {
                array_push($array_emails, $value['email']);
            }
            
            return Mail::to($array_emails)->send(new EmailNotification(json_encode($array_ids), 'assigned_courses'));

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }
}
