<?php

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
Route::post("login", "ApiController@login");

Route::middleware('auth:api')->group(function ()
{
    Route::post("add-task", "ApiController@addTask");
    Route::post("edit-task/{id}", "ApiController@editTask");
    Route::post("delete-task/{id}", "ApiController@deleteTask");
    Route::post('clear-task-list', 'ApiController@clearTaskList');

    Route::post('completed-task', 'ApiController@completedTask');
    Route::post('undo-completed-task', 'ApiController@undoCompletedTask');
});



