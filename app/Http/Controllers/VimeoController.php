<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Vimeo\Vimeo;
use Exception;

define("CLIENT_ID", env("CLIENT_ID_VIMEO"));
define("CLIENT_SECRET", env("CLIENT_SECRET_VIMEO"));
define("ACCESS_TOKEN", env("ACCESS_TOKEN_VIMEO"));

class VimeoController extends Controller
{
    public function syncCourseStructure(Request $request)
    {

        try {

            $courses = [];
            $val = [];
            
            $client = new Vimeo(CLIENT_ID, CLIENT_SECRET, ACCESS_TOKEN);

            $response = $client->request('/me/projects', array(), 'GET');

            $arrayData = $response['body']['data'];

            foreach ($arrayData as $key => $value) {

                $dataInsert = [];

                $id = explode("/", $value['uri']);

                $dataInsert = array(
                    'name' => $value['name'],
                    'vimeo_id' => $id[4],
                    'state' => 1,
                    'free_video' => 0
                );

                $request->request->add($dataInsert);

                $courseCreated = CourseController::store($request);

                $course = json_decode($courseCreated, true);

                if(isset($course['id'])){

                    $courses['course_name'] = $value['name'];

                    $uri_video = $value['metadata']['connections']['videos']['uri'];

                    $lessonsCreated = $this->consult_project_videos($request, $uri_video, $course['id']);

                    $courses['lessons'] = $lessonsCreated;

                    array_push($val, $courses);

                }

            }

            if (empty($val))
                throw new Exception("No hubo inserción de cursos, verifique si ya se existen en la base de datos");
            

            return response()->json(['message' => 'cursos y lecciones creadas', 'data' => $val]);
        
        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    public static function consult_project_videos($request, $uri_video, $course_id)
    {
        try {

            $client = new Vimeo(CLIENT_ID, CLIENT_SECRET, ACCESS_TOKEN);
            
            $videos = $client->request($uri_video, array(), 'GET');
            $video_information = [];

            $data_videos = $videos['body']['data'];

            $lessons_created = [];
            
            foreach ($data_videos as $k => $item) {

                $id = explode("/", $item['uri']);

                $video_information['player_embed_url'] = $item['player_embed_url'];
                $video_information['name'] = $item['name'];
                $video_information['description'] = $item['description'];
                $video_information['picture'] = $item['pictures']['base_link'];
                $video_information['course_id'] = $course_id;
                $video_information['vimeo_id'] = $id[2];

                $request->request->add($video_information);

                $lessonsCreated = LessonController::store($request);

                if( isset( json_decode($lessonsCreated->getContent())->status ) ){
                    array_push($lessons_created, $item['name']);
                }

            }

            return $lessons_created;


        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }
}
