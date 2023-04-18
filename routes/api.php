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

Route::get('/', function (Request $request) {
    return 'OK Success';
});

Route::get('auth', function (Request $request) {
    return new App\Http\Resources\AuthUserResource(Auth::user());
})->middleware(['auth:api']);

Route::post('logout', function(Request $request){
    $accessToken = Auth::user()->token();
    $accessToken->revoke();
    $accessToken->delete();
    return response()->json(null, 204);
})->middleware(['auth:api']);

Route::group(['middleware' => ['capture-request']], function () {
    Route::get('select/user', 'UsersController@select')->middleware([
        'auth:api',
        'can:branch-index',
    ]);

    Route::get('select/branch', 'BranchesController@select')->middleware([
        'auth:api',
        'can:user-index',
    ]);
    
    Route::get('user', 'UsersController@index')->middleware(['auth:api', 'can:user-index']);
    Route::get('user/{id}', 'UsersController@show')->middleware(['auth:api', 'can:user-read']);
    Route::post('user', 'UsersController@store')->middleware(['auth:api', 'can:user-create']);
    Route::put('user/{id}', 'UsersController@update')->middleware(['auth:api', 'can:user-update']);
    
    Route::post('password/update', 'UsersController@updatePassword')->middleware(['auth:api']);
    Route::put('password/reset/{id}', 'UsersController@resetPassword')->middleware(['auth:api']);
    
    Route::get('branch', 'BranchesController@index')->middleware(['auth:api', 'can:branch-index']);
    Route::get('branch/{id}','BranchesController@show')->middleware(['auth:api', 'can:branch-read']);
    Route::post('branch', 'BranchesController@store')->middleware(['auth:api', 'can:branch-create']);
    Route::put('branch/{id}', 'BranchesController@update')->middleware(['auth:api', 'can:branch-update']);
    Route::delete('branch/{id}','BranchesController@destroy')->middleware(['auth:api', 'can:branch-delete']);

    Route::get('webuser', 'WebUsersController@index')->middleware('auth:api', 'can:webuser-index');
});

Route::get('select/branchWeb', 'BranchesController@select');
Route::post('formdatajemaat', 'WebUsersController@store');

