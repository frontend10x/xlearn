<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\companies\group\GroupController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Contact_usController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\Ingreso\LoginController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubcompaniesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\RegisterRequestController;
use App\Http\Controllers\TypeUserController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\DiagnosticController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\VimeoController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\CertificateController;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

define("API_VERSION", env('API_VERSION'));

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post(API_VERSION. '/login',[LoginController::class, 'ingreso']);
Route::post(env('API_VERSION') . '/contact_us/store',[Contact_usController::class, 'store']);

// ---------------------------------------------------------------------
// Countrys
// ---------------------------------------------------------------------    
Route::get(API_VERSION . '/countries/list',[CountryController::class, 'index']);

    
// ---------------------------------------------------------------------
// Register Request
// ---------------------------------------------------------------------

Route::post(env('API_VERSION') . '/register_requests/store',[RegisterRequestController::class, 'store']);

// ---------------------------------------------------------------------
// Plan
// ---------------------------------------------------------------------    

Route::get(env('API_VERSION') . '/plan/list',[PlanController::class, 'index']);

// ---------------------------------------------------------------------
// Tamaños de empresas
// ---------------------------------------------------------------------

Route::get(env('API_VERSION') . '/size/list',[SizeController::class, 'index']);

// ---------------------------------------------------------------------
// Contenido
// ---------------------------------------------------------------------

Route::get(env('API_VERSION') . '/content/list',[ContentController::class, 'index']);

// ---------------------------------------------------------------------
// Cursos
// ---------------------------------------------------------------------    

Route::get(env('API_VERSION') . '/course/list',[CourseController::class, 'index']);
Route::get(API_VERSION . '/course/show/{id}',[CourseController::class, 'show']);

// ---------------------------------------------------------------------
// Lesson
// ---------------------------------------------------------------------  
Route::get(API_VERSION . '/lesson/show_course/{courseId}',[LessonController::class, 'show_course']);

// ---------------------------------------------------------------------
// Usuarios
// --------------------------------------------------------------------- 

Route::get(API_VERSION . '/user/changestate/{id}',[UserController::class, 'changestate']);
Route::post(API_VERSION . '/user/forgot_password',[UserController::class, 'forgot_password']);
Route::put(API_VERSION . '/user/recover_password/{id}',[UserController::class, 'recover_password']);

// ---------------------------------------------------------------------
// Transacciones
// --------------------------------------------------------------------- 

Route::post(API_VERSION . '/transactions/store',[TransactionController::class, 'store']);

// ---------------------------------------------------------------------
// Certificados
// --------------------------------------------------------------------- 
      
Route::get(API_VERSION . '/certificate/show/{code}',[CertificateController::class, 'show']);
Route::get(API_VERSION . '/certificate/download/{code}',[CertificateController::class, 'dowmload']);

// ---------------------------------------------------------------------
// FAQS
// ---------------------------------------------------------------------

Route::get(API_VERSION . '/questions/list_faqs',[QuestionController::class, 'faqs']);


Route::group(['middleware'=>'auth:api' ],function(){

    // ---------------------------------------------------------------------
    // CONTACT
    // ---------------------------------------------------------------------
    
    Route::get(API_VERSION . '/contact_us/list',[Contact_usController::class, 'index']);

    // ---------------------------------------------------------------------
    // EMPRESA
    // ---------------------------------------------------------------------
    
    Route::get(API_VERSION . '/empresa/data',[CompanyController::class, 'index']);
    Route::post(API_VERSION . '/empresa/store',[CompanyController::class, 'store']);

    // ---------------------------------------------------------------------
    // Sub Empresa
    // ---------------------------------------------------------------------

    Route::get(API_VERSION . '/subempresa/datacompanyxid/{id}',[SubcompaniesController::class, 'dataCompanyXId']);
    Route::get(API_VERSION . '/subempresa/data',[CompanyController::class, 'index']);
    Route::get(API_VERSION . '/subempresa/list',[SubcompaniesController::class, 'index']);
    Route::put(API_VERSION . '/subempresa/edit/{id}',[SubcompaniesController::class, 'edit']);
    Route::post(API_VERSION . '/subempresa/store',[SubcompaniesController::class, 'store']);
    Route::put(API_VERSION . '/subempresa/changestate/{id}',[SubcompaniesController::class, 'changestate']);
    Route::post(API_VERSION . '/subempresa/rut',[SubcompaniesController::class, 'uploadRut']);

    // ---------------------------------------------------------------------
    // Users
    // ---------------------------------------------------------------------
    
    Route::get(API_VERSION . '/user/datauser/{id}',[UserController::class, 'datauser']);
    Route::get(API_VERSION . '/user/list',[UserController::class, 'index']);
    Route::put(API_VERSION . '/user/edit/{id}',[UserController::class, 'edit']);
    Route::post(API_VERSION . '/user/store',[UserController::class, 'store']);
    Route::put(API_VERSION . '/user/change_password',[UserController::class, 'change_password']);
    Route::post(API_VERSION . '/user/coursesfavorites',[UserController::class, 'coursesFavorites']);
    Route::post(API_VERSION . '/user/bulk_upload_users',[UserController::class, 'bulkUploadUsers']);
    Route::get(API_VERSION . '/user/sub_companies_withou_group',[UserController::class, 'showUserWithoutGroup']);
    Route::get(API_VERSION . '/sub_companies/users/{id}',[UserController::class, 'showUserSubCompanie']);
    Route::delete(API_VERSION . '/user/delete/{id}',[UserController::class, 'delete']);

    // ---------------------------------------------------------------------
    // Progreso
    // ---------------------------------------------------------------------
    Route::post(API_VERSION . '/progress/store',[ProgressController::class, 'progress_store']);
    Route::get(API_VERSION . '/progress/user',[ProgressController::class, 'check_user_progress']);
    
    // ---------------------------------------------------------------------
    // Grupos usuarios
    // ---------------------------------------------------------------------
    
    Route::get(API_VERSION . '/group/list',[GroupController::class, 'index']);
    Route::put(API_VERSION . '/group/edit/{id}',[GroupController::class, 'edit']);
    Route::post(API_VERSION . '/group/store',[GroupController::class, 'store']);
    Route::put(API_VERSION . '/group/changestate/{id}',[GroupController::class, 'changestate']);
    Route::delete(API_VERSION . '/group/delete/{id}',[GroupController::class, 'delete']);
    Route::get(API_VERSION . '/group/list_company_group/{subcompanie_id}',[GroupController::class, 'listCompanyGroups']);

    // ---------------------------------------------------------------------
    // Areas
    // ---------------------------------------------------------------------    

    Route::get(API_VERSION . '/area/list',[AreaController::class, 'index']);
    Route::put(API_VERSION . '/area/edit/{id}',[AreaController::class, 'edit']);
    Route::post(API_VERSION . '/area/store',[AreaController::class, 'store']);
    Route::put(API_VERSION . '/area/changestate/{id}',[AreaController::class, 'changestate']);

    // ---------------------------------------------------------------------
    // Plan
    // ---------------------------------------------------------------------    

    Route::put(API_VERSION . '/plan/edit/{id}',[PlanController::class, 'edit']);
    Route::post(API_VERSION . '/plan/store',[PlanController::class, 'store']);
    Route::put(API_VERSION . '/plan/changestate/{id}',[PlanController::class, 'changestate']);

    // ---------------------------------------------------------------------
    // Cursos
    // ---------------------------------------------------------------------    
    
    Route::get(API_VERSION . '/course/show_area/{areaId}',[CourseController::class, 'show_area']);
    Route::get(API_VERSION . '/course/show_user/{userId}',[CourseController::class, 'show_user']);
    Route::put(API_VERSION . '/course/edit/{id}',[CourseController::class, 'edit']);
    Route::post(API_VERSION . '/course/store',[CourseController::class, 'store']);
    Route::put(API_VERSION . '/course/changestate/{id}',[CourseController::class, 'changestate']);
    Route::put(API_VERSION . '/course/userrating/{id_curso}',[CourseController::class, 'userrating']);
    
    // ---------------------------------------------------------------------
    // Usuarios x Grupo
    // ---------------------------------------------------------------------  

    Route::post(API_VERSION . '/groupuser/assignment/{group_id}',[GroupController::class, 'assignment']);
    Route::delete(API_VERSION . '/groupuser/removefromgroup/{group_id}',[GroupController::class, 'removefromgroup']);
    Route::get(API_VERSION . '/groupuser/list_user_groups/{userId}',[GroupController::class, 'listUserGroups']);
    Route::get(API_VERSION . '/groupuser/list_group_users/{group_id}',[GroupController::class, 'listGroupUsers']);
    
    // ---------------------------------------------------------------------
    // Categorias
    // ---------------------------------------------------------------------    

    Route::get(API_VERSION . '/category/list',[CategoryController::class, 'index']);
    Route::put(API_VERSION . '/category/edit/{id}',[CategoryController::class, 'edit']);
    Route::post(API_VERSION . '/category/store',[CategoryController::class, 'store']);
    Route::put(API_VERSION . '/category/changestate/{id}',[CategoryController::class, 'changestate']);

    // ---------------------------------------------------------------------
    // Lecciones
    // ---------------------------------------------------------------------    
    Route::get(API_VERSION . '/lesson/list',[LessonController::class, 'index']);
    Route::put(API_VERSION . '/lesson/edit/{id}',[LessonController::class, 'edit']);
    Route::post(API_VERSION . '/lesson/store',[LessonController::class, 'store']);
    Route::put(API_VERSION . '/lesson/changestate/{id}',[LessonController::class, 'changestate']);
    Route::post(API_VERSION . '/lesson/addcommentuser/{id}',[LessonController::class, 'addComment']);
    Route::get(API_VERSION . '/lesson/listComment/{id}',[LessonController::class, 'listComment']);

    // ---------------------------------------------------------------------
    // Register Request
    // ---------------------------------------------------------------------
    
    Route::get(API_VERSION . '/register_requests/list',[RegisterRequestController::class, 'index']);
    Route::put(API_VERSION . '/register_requests/edit/{id}',[RegisterRequestController::class, 'edit']);

    // ---------------------------------------------------------------------
    // Tipos de usuarios
    // ---------------------------------------------------------------------
    
    Route::get(API_VERSION . '/types_users/list',[TypeUserController::class, 'index']);

    // ---------------------------------------------------------------------
    // Roles de usuarios
    // ---------------------------------------------------------------------
    
    Route::get(API_VERSION . '/roles/list',[RolesController::class, 'index']);

    // ---------------------------------------------------------------------
    // Preguntas
    // ---------------------------------------------------------------------
    
    Route::get(API_VERSION . '/questions/list',[QuestionController::class, 'index']);

    // ---------------------------------------------------------------------
    // Diagnostico
    // ---------------------------------------------------------------------
    
    Route::post(API_VERSION . '/diagnostic/store',[DiagnosticController::class, 'store']);
    Route::patch(API_VERSION . '/diagnostic/confirm_route/{diagnostic_id}',[DiagnosticController::class, 'confirm_route']);

    // ---------------------------------------------------------------------
    // Evaluations
    // ---------------------------------------------------------------------
    
    Route::get(API_VERSION . '/evaluation/course',[EvaluationController::class, 'showCourse']);

    // ---------------------------------------------------------------------
    // Respuestas
    // --------------------------------------------------------------------- 
      
    Route::post(API_VERSION . '/answer/store',[AnswerController::class, 'store']);

    // ---------------------------------------------------------------------
    // Certificados
    // --------------------------------------------------------------------- 
      
    Route::post(API_VERSION . '/certificate/generate',[CertificateController::class, 'generate']);

    // ---------------------------------------------------------------------
    // Pagos
    // ---------------------------------------------------------------------
    
    Route::post(API_VERSION . '/payment/requests',[PaymentController::class, 'paymentRequests']);
    Route::post(API_VERSION . '/payment/approved_payment_status',[PaymentController::class, 'approvedPaymentStatus']);

    // ---------------------------------------------------------------------
    // Export
    // --------------------------------------------------------------------- 
      
    Route::get(API_VERSION . '/export/sample_file',[ExportController::class, 'exportSampleFile']);

    // ---------------------------------------------------------------------
    // Vimeo
    // --------------------------------------------------------------------- 
      
    Route::patch(API_VERSION . '/vimeo/sync_course_structure',[VimeoController::class, 'syncCourseStructure']);
    

});