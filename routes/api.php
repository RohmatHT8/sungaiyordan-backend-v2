<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

Route::get('auth', function (Request $request) {
    return new App\Http\Resources\AuthUserResource(Auth::user());
})->middleware(['auth:api']);

Route::post('user', 'UsersController@store')->middleware(['can:user-create']);
Route::post('login', 'UsersController@login');

Route::group(['middleware' => ['capture-request']], function () {
    Route::get('user', 'UsersController@index')->middleware(['auth:api', 'can:user-index']);
    Route::get('user/{id}', 'UsersController@show')->middleware(['auth:api', 'can:user-read']);
    Route::put('user/{id}', 'UsersController@update')->middleware(['auth:api', 'can:user-update']);
    
    Route::post('password/update', 'UsersController@updatePassword')->middleware(['auth:api']);
    Route::put('password/reset/{id}', 'UsersController@resetPassword')->middleware(['auth:api']);
    
    Route::get('branch', 'BranchesController@index')->middleware(['auth:api', 'can:branch-index']);
    Route::post('branch', 'BranchesController@store')->middleware(['auth:api', 'can:branch-create']);
    Route::put('branch', 'BranchesController@update')->middleware(['auth:api', 'can:branch-update']);
});

