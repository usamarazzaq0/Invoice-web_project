<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->name('auth.login');

Route::middleware('auth:api')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::get('classroom/list', [ClassroomController::class, 'list']);
    Route::post('classroom/join', [ClassroomController::class, 'join']);
    Route::post('classroom/leave', [ClassroomController::class, 'leave']);
    Route::get('classroom/{classroom}/listClassroomUsers', [ClassroomController::class, 'listClassroomUsers']);


    Route::post('assignment/{assignment}/submit', [AssignmentController::class, 'submit']);
    Route::post('assignment/{assignment}/mark', [AssignmentController::class, 'mark']);

    Route::post('quiz/{quiz}/submit', [QuizController::class, 'submit']);
    Route::post('quiz/{quiz}/mark', [QuizController::class, 'mark']);

    Route::post('project/{project}/submit', [ProjectController::class, 'submit']);
    Route::post('project/{project}/mark', [ProjectController::class, 'mark']);

    Route::get('report/{classroom}/{user?}', [ReportController::class, 'index']);
    Route::post('report/{classroom}/{user}', [ReportController::class, 'store']);

    Route::apiResource('user', UserController::class);
    Route::apiResource('classroom', ClassroomController::class);
    Route::apiResource('classroom/{classroom}/lecture', LectureController::class);
    Route::apiResource('classroom/{classroom}/assignment', AssignmentController::class);
    Route::apiResource('classroom/{classroom}/quiz', QuizController::class);
    Route::apiResource('classroom/{classroom}/project', ProjectController::class);
});
