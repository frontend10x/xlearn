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

Route::post('/login',[LoginController::class, 'ingreso']);
Route::post('/contact_us/store',[Contact_usController::class, 'store']);

Route::group(['middleware'=>'auth:api' ],function(){

    // ---------------------------------------------------------------------
    // CONTACT
    // ---------------------------------------------------------------------
    
    Route::get('/contact_us/list',[Contact_usController::class, 'index']);

    // ---------------------------------------------------------------------
    // EMPRESA
    // ---------------------------------------------------------------------
    
    Route::get('/empresa/data',[CompanyController::class, 'index']);
    Route::post('/empresa/store',[CompanyController::class, 'store']);

    // ---------------------------------------------------------------------
    // Sub Empresa
    // ---------------------------------------------------------------------

    Route::get('/subempresa/datacompanyxid/{id}',[SubcompaniesController::class, 'dataCompanyXId']);
    Route::get('/subempresa/data',[CompanyController::class, 'index']);
    Route::get('/subempresa/list',[SubcompaniesController::class, 'index']);
    Route::put('/subempresa/edit/{id}',[SubcompaniesController::class, 'edit']);
    Route::post('/subempresa/store',[SubcompaniesController::class, 'store']);
    Route::put('/subempresa/changestate/{id}',[SubcompaniesController::class, 'changestate']);

    // ---------------------------------------------------------------------
    // Users
    // ---------------------------------------------------------------------
    
    Route::get('/user/datauser/{id}',[UserController::class, 'datauser']);
    Route::get('/user/list',[UserController::class, 'index']);
    Route::put('/user/edit/{id}',[UserController::class, 'edit']);
    Route::post('/user/store',[UserController::class, 'store']);
    Route::put('/user/changestate/{id}',[UserController::class, 'changestate']);
    Route::put('/user/forgotpassword/{id}',[UserController::class, 'forgotpassword']);
    Route::put('/user/changepassword',[UserController::class, 'changepassword']);
    Route::post('/user/coursesfavorites',[UserController::class, 'coursesFavorites']);
    
    // ---------------------------------------------------------------------
    // Grupos usuarios
    // ---------------------------------------------------------------------
    
    Route::get('/group/list',[GroupController::class, 'index']);
    Route::put('/group/edit/{id}',[GroupController::class, 'edit']);
    Route::post('/group/store',[GroupController::class, 'store']);
    Route::put('/group/changestate/{id}',[GroupController::class, 'changestate']);
    Route::delete('/group/delete/{id}',[GroupController::class, 'delete']);

    // ---------------------------------------------------------------------
    // Areas
    // ---------------------------------------------------------------------    

    Route::get('/area/list',[AreaController::class, 'index']);
    Route::put('/area/edit/{id}',[AreaController::class, 'edit']);
    Route::post('/area/store',[AreaController::class, 'store']);
    Route::put('/area/changestate/{id}',[AreaController::class, 'changestate']);

    // ---------------------------------------------------------------------
    // Plan
    // ---------------------------------------------------------------------    

    Route::get('/plan/list',[PlanController::class, 'index']);
    Route::put('/plan/edit/{id}',[PlanController::class, 'edit']);
    Route::post('/plan/store',[PlanController::class, 'store']);
    Route::put('/plan/changestate/{id}',[PlanController::class, 'changestate']);

    // ---------------------------------------------------------------------
    // Plan
    // ---------------------------------------------------------------------    
    
    Route::get('/course/list',[CourseController::class, 'index']);
    Route::put('/course/edit/{id}',[CourseController::class, 'edit']);
    Route::post('/course/store',[CourseController::class, 'store']);
    Route::put('/course/changestate/{id}',[CourseController::class, 'changestate']);

    Route::put('/course/userrating/{id_curso}',[CourseController::class, 'userrating']);
    
    // ---------------------------------------------------------------------
    // Usuarios x Grupo
    // ---------------------------------------------------------------------  

    Route::post('/groupuser/assignment/{group_id}',[GroupController::class, 'assignment']);
    Route::delete('/groupuser/removefromgroup/{group_id}',[GroupController::class, 'removefromgroup']);
    Route::get('/groupuser/listUserGroup/{idgroup}',[GroupController::class, 'listUserGroup']);
    
    // ---------------------------------------------------------------------
    // Categorias
    // ---------------------------------------------------------------------    

    Route::get('/category/list',[CategoryController::class, 'index']);
    Route::put('/category/edit/{id}',[CategoryController::class, 'edit']);
    Route::post('/category/store',[CategoryController::class, 'store']);
    Route::put('/category/changestate/{id}',[CategoryController::class, 'changestate']);

    // ---------------------------------------------------------------------
    // Lecciones
    // ---------------------------------------------------------------------    

    Route::get('/lesson/list',[LessonController::class, 'index']);
    Route::put('/lesson/edit/{id}',[LessonController::class, 'edit']);
    Route::post('/lesson/store',[LessonController::class, 'store']);
    Route::put('/lesson/changestate/{id}',[LessonController::class, 'changestate']);
    Route::post('/lesson/addcommentuser/{id}',[LessonController::class, 'addComment']);
    Route::get('/lesson/listComment/{id}',[LessonController::class, 'listComment']);

});


