<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\companies\group\GroupController;
use App\Http\Controllers\companyController;
use App\Http\Controllers\Contact_usController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\Ingreso\LoginController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubcompaniesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\RegisterRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post(env('API_VERSION') . '/login',[LoginController::class, 'ingreso']);
Route::post(env('API_VERSION') . '/contact_us/store',[Contact_usController::class, 'store']);

Route::group(['middleware'=>'auth:api' ],function(){

    $API_VERSION = env('API_VERSION');

    // ---------------------------------------------------------------------
    // CONTACT
    // ---------------------------------------------------------------------
    
    Route::get($API_VERSION . '/contact_us/list',[Contact_usController::class, 'index']);

    // ---------------------------------------------------------------------
    // EMPRESA
    // ---------------------------------------------------------------------
    
    Route::get($API_VERSION . '/empresa/data',[CompanyController::class, 'index']);
    Route::post($API_VERSION . '/empresa/store',[CompanyController::class, 'store']);

    // ---------------------------------------------------------------------
    // Sub Empresa
    // ---------------------------------------------------------------------

    Route::get($API_VERSION . '/subempresa/datacompanyxid/{id}',[SubcompaniesController::class, 'dataCompanyXId']);
    Route::get($API_VERSION . '/subempresa/data',[CompanyController::class, 'index']);
    Route::get($API_VERSION . '/subempresa/list',[SubcompaniesController::class, 'index']);
    Route::put($API_VERSION . '/subempresa/edit/{id}',[SubcompaniesController::class, 'edit']);
    Route::post($API_VERSION . '/subempresa/store',[SubcompaniesController::class, 'store']);
    Route::put($API_VERSION . '/subempresa/changestate/{id}',[SubcompaniesController::class, 'changestate']);

    // ---------------------------------------------------------------------
    // Users
    // ---------------------------------------------------------------------
    
    Route::get($API_VERSION . '/user/datauser/{id}',[UserController::class, 'datauser']);
    Route::get($API_VERSION . '/user/list',[UserController::class, 'index']);
    Route::put($API_VERSION . '/user/edit/{id}',[UserController::class, 'edit']);
    Route::post($API_VERSION . '/user/store',[UserController::class, 'store']);
    Route::get($API_VERSION . '/user/changestate/{id}',[UserController::class, 'changestate']);
    Route::put($API_VERSION . '/user/forgotpassword/{id}',[UserController::class, 'forgotpassword']);
    Route::put($API_VERSION . '/user/changepassword',[UserController::class, 'changepassword']);
    Route::post($API_VERSION . '/user/coursesfavorites',[UserController::class, 'coursesFavorites']);
    
    // ---------------------------------------------------------------------
    // Grupos usuarios
    // ---------------------------------------------------------------------
    
    Route::get($API_VERSION . '/group/list',[GroupController::class, 'index']);
    Route::put($API_VERSION . '/group/edit/{id}',[GroupController::class, 'edit']);
    Route::post($API_VERSION . '/group/store',[GroupController::class, 'store']);
    Route::put($API_VERSION . '/group/changestate/{id}',[GroupController::class, 'changestate']);
    Route::delete($API_VERSION . '/group/delete/{id}',[GroupController::class, 'delete']);

    // ---------------------------------------------------------------------
    // Areas
    // ---------------------------------------------------------------------    

    Route::get($API_VERSION . '/area/list',[AreaController::class, 'index']);
    Route::put($API_VERSION . '/area/edit/{id}',[AreaController::class, 'edit']);
    Route::post($API_VERSION . '/area/store',[AreaController::class, 'store']);
    Route::put($API_VERSION . '/area/changestate/{id}',[AreaController::class, 'changestate']);

    // ---------------------------------------------------------------------
    // Plan
    // ---------------------------------------------------------------------    

    Route::get($API_VERSION . '/plan/list',[PlanController::class, 'index']);
    Route::put($API_VERSION . '/plan/edit/{id}',[PlanController::class, 'edit']);
    Route::post($API_VERSION . '/plan/store',[PlanController::class, 'store']);
    Route::put($API_VERSION . '/plan/changestate/{id}',[PlanController::class, 'changestate']);

    // ---------------------------------------------------------------------
    // Plan
    // ---------------------------------------------------------------------    
    
    Route::get($API_VERSION . '/course/list',[CourseController::class, 'index']);
    Route::put($API_VERSION . '/course/edit/{id}',[CourseController::class, 'edit']);
    Route::post($API_VERSION . '/course/store',[CourseController::class, 'store']);
    Route::put($API_VERSION . '/course/changestate/{id}',[CourseController::class, 'changestate']);

    Route::put($API_VERSION . '/course/userrating/{id_curso}',[CourseController::class, 'userrating']);
    
    // ---------------------------------------------------------------------
    // Usuarios x Grupo
    // ---------------------------------------------------------------------  

    Route::post($API_VERSION . '/groupuser/assignment/{group_id}',[GroupController::class, 'assignment']);
    Route::delete($API_VERSION . '/groupuser/removefromgroup/{group_id}',[GroupController::class, 'removefromgroup']);
    Route::get($API_VERSION . '/groupuser/listUserGroup/{idgroup}',[GroupController::class, 'listUserGroup']);
    
    // ---------------------------------------------------------------------
    // Categorias
    // ---------------------------------------------------------------------    

    Route::get($API_VERSION . '/category/list',[CategoryController::class, 'index']);
    Route::put($API_VERSION . '/category/edit/{id}',[CategoryController::class, 'edit']);
    Route::post($API_VERSION . '/category/store',[CategoryController::class, 'store']);
    Route::put($API_VERSION . '/category/changestate/{id}',[CategoryController::class, 'changestate']);

    // ---------------------------------------------------------------------
    // Lecciones
    // ---------------------------------------------------------------------    

    Route::get($API_VERSION . '/lesson/list',[LessonController::class, 'index']);
    Route::put($API_VERSION . '/lesson/edit/{id}',[LessonController::class, 'edit']);
    Route::post($API_VERSION . '/lesson/store',[LessonController::class, 'store']);
    Route::put($API_VERSION . '/lesson/changestate/{id}',[LessonController::class, 'changestate']);
    Route::post($API_VERSION . '/lesson/addcommentuser/{id}',[LessonController::class, 'addComment']);
    Route::get($API_VERSION . '/lesson/listComment/{id}',[LessonController::class, 'listComment']);

    // ---------------------------------------------------------------------
    // Countrys
    // ---------------------------------------------------------------------    
    Route::get($API_VERSION . '/countries',[CountryController::class, 'index']);

    // ---------------------------------------------------------------------
    // Register Request
    // ---------------------------------------------------------------------
    
    Route::get($API_VERSION . '/register_requests/list',[RegisterRequestController::class, 'index']);
    Route::post($API_VERSION . '/register_requests/store',[RegisterRequestController::class, 'store']);
    Route::put($API_VERSION . '/register_requests/edit/{id}',[RegisterRequestController::class, 'edit']);
    

});


