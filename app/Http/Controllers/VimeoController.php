<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Vimeo\Vimeo;
use Exception;

define("CLIENT_ID", env("CLIENT_ID_VIMEO"));
define("CLIENT_SECRET", env("CLIENT_SECRET_VIMEO"));
define("ACCESS_TOKEN", env("ACCESS_TOKEN_VIMEO"));

date_default_timezone_set('America/Bogota');

class VimeoController extends Controller
{
    public function __construct()
    {
        $this->trailers = [];
    }

    public function syncCourseStructure(Request $request)
    {

        try {

            $val = [];
            $results = [];
            $currentLevel = [];
            
            $client = new Vimeo(CLIENT_ID, CLIENT_SECRET, ACCESS_TOKEN);

            $response = $client->request('/me/projects', array(), 'GET');

            // return $client->request('/users/177726805/projects/16243850/items', array(), 'GET');

            $arrayData = $response['body']['data'];

            array_walk_recursive($arrayData, function($element, $key) use (&$results, &$currentLevel) {
                // Condición para verificar si el element cumple con tu criterio
                if(strstr($element, 'Área' )){
                    $results[] = [
                        'element' => $element,
                        'uri' => $currentLevel['uri'] // Accede a otros datos en el mismo nivel
                    ];
                }

                if($element == 'Trailers' ){
                    $trailer = $this->process_trailers($currentLevel);
                }

                $currentLevel[$key] = $element;
                
            });

            // Procesamos los trailers
            foreach ($arrayData as $key => $value) {

                //Instanciamos los trailers
                $this->process_trailers($value);
                
            };

            // Procesamos las areas, programas, cursos y lecciones
            foreach ($results as $key => $value) {

                $folder = $client->request($value["uri"], array(), 'GET');
                $processAreas = $this->process_areas($request, $folder["body"]);
                array_push($val, $processAreas);
            }
            
            if (empty($val))
                throw new Exception("No hubo inserción de información, verifique si ya existen en la base de datos");
            

            return response()->json(['message' => 'areas, cursos, programas y lecciones creadas', 'data' => $val]);
        
        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    public function process_trailers($value)
    {
        try {
            
            $client = new Vimeo(CLIENT_ID, CLIENT_SECRET, ACCESS_TOKEN);
            $valTrailers = [];

            if($value['name'] == 'Trailers' ){

                $folders = $value['metadata']['connections']['folders']['uri'];

                $folders = $client->request($folders, array(), 'GET');

                $arrayData = $folders['body']['data'];

                foreach ($arrayData as $key => $file) {

                    $uri_video = $file['folder']['metadata']['connections']['videos']['uri'];

                    $videos = $client->request($uri_video, array(), 'GET');

                    $arrayVideos = $videos['body']['data'];

                    foreach ($arrayVideos as $k => $vid) {
                        
                        $arrayTrailers = [
                            'name' => $vid['name'],
                            'video:uri' => $vid['player_embed_url']
                        ];

                        array_push($valTrailers, $arrayTrailers);

                    }
                }

                $this->trailers = $valTrailers;
                

                //echo json_encode($valTrailers);
                // $this->trailers[]

                // $found_key = array_search('ADN Innovador', array_column($this->trailers, 'name'));


                // echo json_encode($found_key);

            }

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    public function process_areas($request, $value)
    {
        try {

            $client = new Vimeo(CLIENT_ID, CLIENT_SECRET, ACCESS_TOKEN);
            $areas_created = [];
            
            $name = str_replace('Área - ', '', $value['name']);
                    
            $dataArea = array(
                'name' => $name,
                'vimeo:id' => get_id_vimeo($value['uri']),
                'state' => 1,
                'vimeo:uri' => $value['uri']
            );

            $request->request->add($dataArea);

            $areaCreated = AreaController::sync_with_vimeo($request);

            $area = json_decode($areaCreated, true);

            if(isset($area['id'])){

                $areas['area_name'] = $name;

                $uri_folder = $value['metadata']['connections']['folders']['uri'];

                $folders = $client->request($uri_folder, array(), 'GET');

                $arrayCourses = $folders['body']['data'];

                $programsCreated = $this->consult_project_programs($request, $uri_folder, $area['id']);

                $areas['programs'] = $programsCreated;

                array_push($areas_created, $areas);

                return $areas_created;
                                        
            }


        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    public function consult_project_programs($request, $uri_program, $area_id)
    {
        try {
            
            $client = new Vimeo(CLIENT_ID, CLIENT_SECRET, ACCESS_TOKEN);
            $programs_created = [];

            $response = $client->request($uri_program, array(), 'GET');

            $arrayPrograms = $response['body']['data'];

            foreach ($arrayPrograms as $key => $folder) {

                $dataInsert = [];

                $value = $folder['folder'];

                $dataInsert = array(
                    'name' => $value['name'],
                    'vimeo:id' => get_id_vimeo($value['uri']),
                    'area_id' => $area_id,
                    'vimeo:uri' => $value['uri']
                );

                $request->request->add($dataInsert);

                $programCreated = ProgramController::sync_with_vimeo($request);

                $program = json_decode($programCreated, true);

                if($dataInsert['vimeo:id'] == "14375301"){
                    save_file($program);
                }

                if(isset($program['id'])){

                    $programs['program_name'] = $value['name'];

                    $uri_courses = $value['metadata']['connections']['folders']['uri'];

                    $courseCreated = $this->consult_project_courses($request, $uri_courses, $program['id']);

                    $programs['courses'] = $courseCreated;

                    array_push($programs_created, $programs);

                }

            }

            return $programs_created;


        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    public function consult_project_courses($request, $uri_courses, $program_id)
    {
        try {
            
            $client = new Vimeo(CLIENT_ID, CLIENT_SECRET, ACCESS_TOKEN);
            $courses_created = [];

            $response = $client->request($uri_courses, array(), 'GET');

            $arrayCourses = $response['body']['data'];

            foreach ($arrayCourses as $key => $folder) {

                $dataInsert = [];

                $value = $folder['folder'];

                // Consulta de trailer para el curso
                $found_key = array_search($value['name'], array_column($this->trailers, 'name'));

                $dataInsert = array(
                    'name' => $value['name'],
                    'vimeo_id' => get_id_vimeo($value['uri']),
                    'state' => 1,
                    'free_video' => 0,
                    'video_path' => $this->trailers[$found_key]['video:uri'],
                    'program_id' => $program_id,
                    'video_uri' => $value['metadata']['connections']['videos']['uri']
                );

                $request->request->add($dataInsert);

                $courseCreated = CourseController::sync_with_vimeo($request);

                $course = json_decode($courseCreated, true);

                if(isset($course['id'])){

                    $courses['course_name'] = $value['name'];

                    $uri_video = $value['metadata']['connections']['videos']['uri'];

                    $lessonsCreated = $this->consult_project_videos($request, $uri_video, $course['id']); 

                    $courses['lessons'] = $lessonsCreated;

                    array_push($courses_created, $courses);

                }

            }

            return $courses_created;


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
                $video_information['duration'] = $item['duration'];
                $video_information['description'] = $item['description'];
                $video_information['picture'] = $item['pictures']['base_link'];
                $video_information['course_id'] = $course_id;
                $video_information['vimeo_id'] = $id[2];
                $video_information['modified_time'] = $item['parent_folder']['modified_time'];
                $video_information['vimeo_order'] = intval($item['name'][0].$item['name'][1]);

                $request->request->add($video_information);

                $lessonsCreated = LessonController::sync_with_vimeo($request);

                $lessons = json_decode($lessonsCreated, true);

                if( isset( $lessons['status'] ) ){
                    array_push($lessons_created, $item['name'] .' - '.$video_information['modified_time']);
                }

            }

            return $lessons_created;


        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }
}