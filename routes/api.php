<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

// Route::get('/', function (Request $request) {
//     return 'OK Success';
// });

Route::get('auth', function (Request $request) {
    return new App\Http\Resources\AuthUserResource(Auth::user());
})->middleware(['auth:api']);

Route::post('logout', function (Request $request) {
    $accessToken = Auth::user()->token();
    $accessToken->revoke();
    $accessToken->delete();
    return response()->json(null, 204);
})->middleware(['auth:api']);

Route::group(['middleware' => ['capture-request']], function () {
    Route::get('widget/barchart', 'UsersController@barchart')->middleware(['auth:api', 'can:widget-barchart']);;

    Route::get('select/user', 'UsersController@select')->middleware([
        'auth:api',
        'can:branch-index',
    ]);

    Route::get('select/usersertificate', 'UsersController@selectsertificate')
        ->middleware([
            'auth:api',
            'can:user-index' .
                '| branch-index' .
                '| familycard-index' .
                '| shdr-index' .
                '| baptism-index' .
                '| childsubmission-index' .
                '| marriagecertificate-index' .
                '| confirmationofmarriage-index',
        ]);

    Route::get('select/branch', 'BranchesController@select')->middleware([
        'auth:api',
        'can:user-index',
    ]);

    Route::get('select/permission', 'PermissionsController@select')->middleware([
        'auth:api', 'can:permissionsetting-index'
    ]);

    Route::get('select/role', 'RolesController@select')->middleware([
        'auth:api', 'can:user-index'
    ]);

    Route::get('select/department', 'DepartmentsController@select')->middleware([
        'auth:api', 'can:role-index'
    ]);

    Route::get('select/transaction', 'TransactionsController@select')->middleware([
        'auth:api', 'can:numbersetting-index'
    ]);
    Route::get('select/reportpermission', 'ReportPermissionsController@select')->middleware([
        'auth:api', 'can:reportpermissionsetting-index'
    ]);

    Route::get('report/jemaat', 'UsersController@jemaat');

    Route::get('user', 'UsersController@index')->middleware(['auth:api', 'can:user-index']);
    Route::get('user/{id}', 'UsersController@show')->middleware(['auth:api', 'can:user-read']);
    Route::post('user', 'UsersController@store')->middleware(['auth:api', 'can:user-create']);
    Route::put('user/{id}', 'UsersController@update')->middleware(['auth:api', 'can:user-update']);
    Route::delete('user/{id}', 'UsersController@destroy')->middleware(['auth:api', 'can:user-delete']);

    Route::post('password/update', 'UsersController@updatePassword')->middleware(['auth:api']);
    Route::put('password/reset/{id}', 'UsersController@resetPassword')->middleware(['auth:api']);

    Route::get('branch', 'BranchesController@index')->middleware(['auth:api', 'can:branch-index']);
    Route::get('branch/{id}', 'BranchesController@show')->middleware(['auth:api', 'can:branch-read']);
    Route::post('branch', 'BranchesController@store')->middleware(['auth:api', 'can:branch-create']);
    Route::put('branch/{id}', 'BranchesController@update')->middleware(['auth:api', 'can:branch-update']);
    Route::delete('branch/{id}', 'BranchesController@destroy')->middleware(['auth:api', 'can:branch-delete']);

    Route::get('department', 'DepartmentsController@index')->middleware(['auth:api', 'can:department-index']);
    Route::get('department/{id}', 'DepartmentsController@show')->middleware(['auth:api', 'can:department-read']);
    Route::post('department', 'DepartmentsController@store')->middleware(['auth:api', 'can:department-create']);
    Route::put('department/{id}', 'DepartmentsController@update')->middleware(['auth:api', 'can:department-update']);
    Route::delete('department/{id}', 'DepartmentsController@destroy')->middleware(['auth:api', 'can:department-delete']);

    Route::get('role', 'RolesController@index')->middleware(['auth:api', 'can:role-index']);
    Route::get('role/{id}', 'RolesController@show')->middleware(['auth:api', 'can:role-read']);
    Route::post('role', 'RolesController@store')->middleware(['auth:api', 'can:role-create']);
    Route::put('role/{id}', 'RolesController@update')->middleware(['auth:api', 'can:role-update']);
    Route::delete('role/{id}', 'RolesController@destroy')->middleware(['auth:api', 'can:role-delete']);

    Route::get('permissionsetting', 'PermissionSettingsController@index')->middleware(['auth:api', 'can:permissionsetting-index']);
    Route::get('permissionsetting/{id}', 'PermissionSettingsController@show')->middleware(['auth:api', 'can:permissionsetting-read']);
    Route::post('permissionsetting', 'PermissionSettingsController@store')->middleware(['auth:api', 'can:permissionsetting-create']);
    Route::put('permissionsetting/{id}', 'PermissionSettingsController@update')->middleware(['auth:api', 'can:permissionsetting-create']);

    Route::get('numbersetting', 'NumberSettingsController@index')->middleware(['auth:api', 'can:numbersetting-index']);
    Route::get('numbersetting/{id}', 'NumberSettingsController@show')->middleware(['auth:api', 'can:numbersetting-read']);
    Route::post('numbersetting', 'NumberSettingsController@store')->middleware(['auth:api', 'can:numbersetting-create']);
    Route::put('numbersetting/{id}', 'NumberSettingsController@update')->middleware(['auth:api', 'can:numbersetting-update']);
    Route::delete('numbersetting/{id}', 'NumberSettingsController@destroy')->middleware(['auth:api', 'can:numbersetting-delete']);
    Route::delete('numbersetting', 'NumberSettingsController@destroyAll')->middleware(['auth:api', 'can:numbersetting-delete']);

    Route::get('shdr', 'ShdrsController@index')->middleware(['auth:api', 'can:shdr-index']);
    Route::get('shdr/{id}', 'ShdrsController@show')->middleware(['auth:api', 'can:shdr-read']);
    Route::post('shdr', 'ShdrsController@store')->middleware(['auth:api', 'can:shdr-create']);
    Route::put('shdr/{id}', 'ShdrsController@update')->middleware(['auth:api', 'can:shdr-update']);
    Route::delete('shdr/{id}', 'ShdrsController@destroy')->middleware(['auth:api', 'can:shdr-delete']);
    Route::get('shdr/{id}/print', 'ShdrsController@show')->middleware(['auth:api', 'can:shdr-create']);

    Route::get('baptism', 'BaptismsController@index')->middleware(['auth:api', 'can:baptism-index']);
    Route::get('baptism/{id}', 'BaptismsController@show')->middleware(['auth:api', 'can:baptism-read']);
    Route::post('baptism', 'BaptismsController@store')->middleware(['auth:api', 'can:baptism-create']);
    Route::put('baptism/{id}', 'BaptismsController@update')->middleware(['auth:api', 'can:baptism-update']);
    Route::delete('baptism/{id}', 'BaptismsController@destroy')->middleware(['auth:api', 'can:baptism-delete']);

    Route::get('childsubmission', 'ChildSubmissionsController@index')->middleware(['auth:api', 'can:childsubmission-index']);
    Route::get('childsubmission/{id}', 'ChildSubmissionsController@show')->middleware(['auth:api', 'can:childsubmission-read']);
    Route::post('childsubmission', 'ChildSubmissionsController@store')->middleware(['auth:api', 'can:childsubmission-create']);
    Route::put('childsubmission/{id}', 'ChildSubmissionsController@update')->middleware(['auth:api', 'can:childsubmission-update']);
    Route::delete('childsubmission/{id}', 'ChildSubmissionsController@destroy')->middleware(['auth:api', 'can:childsubmission-delete']);

    Route::get('marriagecertificate', 'MarriageCertificatesController@index')->middleware(['auth:api', 'can:marriagecertificate-index']);
    Route::get('marriagecertificate/{id}', 'MarriageCertificatesController@show')->middleware(['auth:api', 'can:marriagecertificate-read']);
    Route::post('marriagecertificate', 'MarriageCertificatesController@store')->middleware('auth:api', 'can:marriagecertificate-create');
    Route::put('marriagecertificate/{id}', 'MarriageCertificatesController@update')->middleware(['auth:api', 'can:marriagecertificate-update']);
    Route::delete('marriagecertificate/{id}', 'MarriageCertificatesController@destroy')->middleware(['auth:api', 'can:marriagecertificate-delete']);

    Route::get('confirmationofmarriage', 'ConfirmationOfMarriagesController@index')->middleware(['auth:api', 'can:confirmationofmarriage-index']);
    Route::get('confirmationofmarriage/{id}', 'ConfirmationOfMarriagesController@show')->middleware(['auth:api', 'can:confirmationofmarriage-read']);
    Route::post('confirmationofmarriage', 'ConfirmationOfMarriagesController@store')->middleware(['auth:api', 'can:confirmationofmarriage-create']);
    Route::put('confirmationofmarriage/{id}', 'ConfirmationOfMarriagesController@update')->middleware(['auth:api', 'can:confirmationofmarriage-update']);
    Route::delete('confirmationofmarriage/{id}', 'ConfirmationOfMarriagesController@destroy')->middleware(['auth:api', 'can:confirmationofmarriage-delete']);

    Route::get('familycard', 'FamilyCardsController@index')->middleware(['auth:api', 'can:familycard-index']);
    Route::get('familycard/{id}', 'FamilyCardsController@show')->middleware(['auth:api', 'can:familycard-read']);
    Route::post('familycard', 'FamilyCardsController@store')->middleware(['auth:api', 'can:familycard-create']);
    Route::put('familycard/{id}', 'FamilyCardsController@update')->middleware(['auth:api', 'can:familycard-update']);
    Route::delete('familycard/{id}', 'FamilyCardsController@destroy')->middleware(['auth:api', 'can:familycard-delete']);

    Route::get('webuser', 'WebUsersController@index')->middleware(['auth:api', 'can:webuser-index']);

    Route::get('webfamilycard', 'WebFamilyCardsController@index')->middleware(['auth:api', 'can:webfamilycard-index']);
    Route::get('webfamilycard/{id}', 'WebFamilyCardsController@show')->middleware(['auth:api', 'can:webfamilycard-read']);
    Route::post('familycardconvert', 'WebFamilyCardsController@convert')->middleware(['auth:api', 'can:webfamilycard-create']);

    Route::get('widget', 'WidgetsController@index')->middleware(['auth:api', 'can:widget-index']);
    Route::get('widget/{id}', 'WidgetsController@show')->middleware(['auth:api', 'can:widget-read']);
    Route::put('widget/{id}', 'WidgetsController@update')->middleware(['auth:api', 'can:widget-update']);

    Route::get('widgetpermissionsetting', 'WidgetPermissionSettingsController@index')->middleware(['auth:api', 'can:widgetpermissionsetting-index']);
    Route::get('widgetpermissionsetting/{id}', 'WidgetPermissionSettingsController@show')->middleware(['auth:api', 'can:widgetpermissionsetting-read']);
    Route::post('widgetpermissionsetting', 'WidgetPermissionSettingsController@store')->middleware(['auth:api', 'can:widgetpermissionsetting-create']);
    Route::put('widgetpermissionsetting/{id}', 'WidgetPermissionSettingsController@update')->middleware(['auth:api', 'can:widgetpermissionsetting-update']);
    Route::delete('widgetpermissionsetting/{id}', 'WidgetPermissionSettingsController@destroy')->middleware(['auth:api', 'can:widgetpermissionsetting-delete']);
    Route::delete('widgetpermissionsetting', 'WidgetPermissionSettingsController@destroyAll')->middleware(['auth:api', 'can:widgetpermissionsetting-delete']);

    Route::get('reportpermissionsetting', 'ReportPermissionSettingsController@index')->middleware(['auth:api', 'can:reportpermissionsetting-index']);
    Route::get('reportpermissionsetting/{id}', 'ReportPermissionSettingsController@show')->middleware(['auth:api', 'can:reportpermissionsetting-read']);
    Route::post('reportpermissionsetting', 'ReportPermissionSettingsController@store')->middleware(['auth:api', 'can:reportpermissionsetting-create']);
    Route::put('reportpermissionsetting/{id}', 'ReportPermissionSettingsController@update')->middleware(['auth:api', 'can:reportpermissionsetting-update']);
    Route::delete('reportpermissionsetting/{id}', 'ReportPermissionSettingsController@destroy')->middleware(['auth:api', 'can:reportpermissionsetting-delete']);
    Route::delete('reportpermissionsetting', 'ReportPermissionSettingsController@destroyAll')->middleware(['auth:api', 'can:reportpermissionsetting-delete']);
});

Route::get('select/branchWeb', 'BranchesController@select');
Route::post('formdatajemaat', 'WebUsersController@store');
// Route::get('webfamilycard', 'WebFamilyCardsController@index');
