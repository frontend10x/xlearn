<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Exception;

use App\Http\Controllers\companies\group\GroupController;


class ReportController extends Controller
{
    private $users;
    private $progress;

    public function getReportsForCompany(Request $request, $id)
    {
        try {

            $reports = [
                "users" => $this->getCompanyActiveUsers($request, $id),
                "team" => $this->getCompanyTeams($request, $id),
                "quotas" => $this->getCompanyQuotas($request, $id),
                "certificates" => $this->getCompanyCertifieds($request, $id),
                "trainingTime" => $this->getCompanyTrainingTime($request, $id),
                "courses" => $this->getCompanyCourses($request, $id),
                "dedicatedHours" => $this->getCompanyDedicatedHours($request, $id),
                "time" => $this->getCompanyTimeElapsed($request, $id)
            ];
            
            return $reports;
            

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    public function getCompanyActiveUsers($request, $id)
    {
        try {

            $this->users = data_mapper(UserController::showUserSubCompanie($request, $id));

            $activeUsers = count_keys($this->users, "currently_active", 1);

            return [
                "active" => $activeUsers,
                "total" => count($this->users)
            ];            

        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    public function getCompanyTeams($request, $id)
    {
        try {

            $groups = new GroupController();
            
            $teams = data_mapper($groups->listCompanyGroups($request, $id), "groups");

            return [
                "total" => count($teams)
            ];
            
        
        } catch (Exception $e) {
            
            return return_exceptions($e);

        }
    }

    public function getCompanyQuotas($request, $id)
    {
        try {

            return [
                // se resta un usuario, debido a que el usuario empresa no es contabilizado
                // en los cupos
                "used" => count($this->users) - 1,
                "total" => SubcompaniesController::getSubCompanieQuotas($id),
            ];

        } catch (Exception $e) {
            
            return return_exceptions($e);

        } 
    }

    public function getCompanyCertifieds($request, $id)
    {
        try {
            
            $certificates = SubcompaniesController::showCerificates($request, $id);

            return [
                "total" => count($certificates)
            ];

        } catch (Exception $e) {
            
            return return_exceptions($e);

        } 
    }

    public function getCompanyTrainingTime($request, $id)
    {
        try {

            $users = get_ids($this->users, "id");
            
            $this->progress = data_mapper(ProgressController::check_user_progress($request, $users), "progress");

            $seconds = 0;

            foreach ($this->progress as $key => $value) {
                $seconds = $seconds + $value["advanced_current_time"];
            }

            return [ "total" => handle_seconds($seconds) ];

        } catch (Exception $e) {
            
            return return_exceptions($e);

        } 
    }

    public function getCompanyCourses($request, $id)
    {
        try {

            $usersCourses = [];
            $coursesCompleted = 0;
            $pendingCourses = 0;
            $percentageCompleted = 0;
            $percentagePending = 0;

            $users = get_ids($this->users, "id");

            $courses = new CourseController();

            foreach ($users as $k => $id) {
                $userCourses = data_mapper($courses->show_user($request, $id));
                foreach ($userCourses as $key => $course) {
                    array_push($usersCourses, $course);
                }
            }

            foreach ($usersCourses as $key => $value) {
                if(count($value)){
                    if($value["progress:porcentage"] >= 100)
                        $coursesCompleted++;
                }
            }

            if( count($usersCourses) ) {
                $pendingCourses = count($usersCourses) - $coursesCompleted;
                $percentageCompleted = $coursesCompleted * 100 / count($usersCourses);
                $percentagePending = $pendingCourses * 100 / count($usersCourses);
            }
            
            return [ 
                "completed" => [
                    'total' => $coursesCompleted,
                    'percentage' => $percentageCompleted . '%'
                ], 
                "pending" => [
                    'total' => $pendingCourses,
                    'percentage' => $percentagePending . '%'
                ], 
                "total" => count($usersCourses) 
            ];
            

        } catch (Exception $e) {
            
            return return_exceptions($e);

        } 
    }

    public function getCompanyDedicatedHours($request, $id)
    {
        try {

            $progressTime = []; 
            $workingHours = 0;
            $nonWorkingHours = 0;

            foreach ($this->progress as $key => $value) {
                
                $hour = date("H:i:s", strtotime($value["updated_at"]));
                $hours[] = $hour;
                $hours[] = is_working_time($hour);

                if(is_working_time($hour)){
                    $workingHours = $workingHours + $value["advanced_current_time"];
                }else{
                    $nonWorkingHours = $nonWorkingHours + $value["advanced_current_time"];
                }
            }

            return [
                "workingHours" => handle_seconds($workingHours),
                "nonWorkingHours" => handle_seconds($nonWorkingHours)
            ];
            
        } catch (Exception $e) {
            
            return return_exceptions($e);

        } 
    }

    public function getCompanyTimeElapsed($request, $id)
    {
        try {

            return SubcompaniesController::validateActiveSubscription($request, $id);

        } catch (Exception $e) {
            
            return return_exceptions($e);

        } 
    }
}