<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\Authcontroller;
use App\Http\Controllers\api\AttachmentController;

use App\Http\Controllers\api\Project;
use App\Http\Controllers\api\Task;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [Authcontroller::class,'register']);
Route::post('/login', [Authcontroller::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [Authcontroller::class,'logout']);

    Route::apiResource('projects',Project::class);

    Route::apiResource('projects.tasks', Task::class)->scoped();


    Route::get('projects/{project}/tasks/{task}/attachments/{attachment}/download',[AttachmentController::class,'download']);
    Route::post('projects/{project}/tasks/{task}/attachments',[AttachmentController::class,'store']);
    Route::delete('projects/{project}/tasks/{task}/attachments/{attachment}',[AttachmentController::class,'destroy']);

    
});






