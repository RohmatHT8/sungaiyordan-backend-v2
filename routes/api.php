<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    Route::get('select/building', 'BuildingsController@select')->middleware(['auth:api']);
    Route::get('select/itemtype', 'ItemTypesController@select')->middleware(['auth:api']);
    Route::get('select/room', 'RoomsController@select')->middleware(['auth:api']);
    Route::get('select/item', 'ItemsController@select')->middleware(['auth:api']);

    Route::get('report/jemaat', 'UsersController@jemaat');
    Route::get('report/finance', 'FinancesController@report');

    Route::get('report/inventory', 'ItemsController@inventory');

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
    Route::get('shdr/{id}/print', 'ShdrsController@downloadDocument')->middleware(['auth:api', 'can:shdr-read']);

    Route::get('baptism', 'BaptismsController@index')->middleware(['auth:api', 'can:baptism-index']);
    Route::get('baptism/{id}', 'BaptismsController@show')->middleware(['auth:api', 'can:baptism-read']);
    Route::post('baptism', 'BaptismsController@store')->middleware(['auth:api', 'can:baptism-create']);
    Route::put('baptism/{id}', 'BaptismsController@update')->middleware(['auth:api', 'can:baptism-update']);
    Route::delete('baptism/{id}', 'BaptismsController@destroy')->middleware(['auth:api', 'can:baptism-delete']);
    Route::get('baptism/{id}/print', 'BaptismsController@downloadDocument')->middleware(['auth:api', 'can:baptism-create']);

    Route::get('childsubmission', 'ChildSubmissionsController@index')->middleware(['auth:api', 'can:childsubmission-index']);
    Route::get('childsubmission/{id}', 'ChildSubmissionsController@show')->middleware(['auth:api', 'can:childsubmission-read']);
    Route::post('childsubmission', 'ChildSubmissionsController@store')->middleware(['auth:api', 'can:childsubmission-create']);
    Route::put('childsubmission/{id}', 'ChildSubmissionsController@update')->middleware(['auth:api', 'can:childsubmission-update']);
    Route::delete('childsubmission/{id}', 'ChildSubmissionsController@destroy')->middleware(['auth:api', 'can:childsubmission-delete']);
    Route::get('childsubmission/{id}/print', 'ChildSubmissionsController@downloadDocument')->middleware(['auth:api', 'can:childsubmission-create']);

    Route::get('marriagecertificate', 'MarriageCertificatesController@index')->middleware(['auth:api', 'can:marriagecertificate-index']);
    Route::get('marriagecertificate/{id}', 'MarriageCertificatesController@show')->middleware(['auth:api', 'can:marriagecertificate-read']);
    Route::post('marriagecertificate', 'MarriageCertificatesController@store')->middleware('auth:api', 'can:marriagecertificate-create');
    Route::put('marriagecertificate/{id}', 'MarriageCertificatesController@update')->middleware(['auth:api', 'can:marriagecertificate-update']);
    Route::delete('marriagecertificate/{id}', 'MarriageCertificatesController@destroy')->middleware(['auth:api', 'can:marriagecertificate-delete']);
    Route::get('marriage/{id}/print', 'MarriageCertificatesController@downloadDocument')->middleware(['auth:api', 'can:marriagecertificate-create']);
    
    Route::get('confirmationofmarriage', 'ConfirmationOfMarriagesController@index')->middleware(['auth:api', 'can:confirmationofmarriage-index']);
    Route::get('confirmationofmarriage/{id}', 'ConfirmationOfMarriagesController@show')->middleware(['auth:api', 'can:confirmationofmarriage-read']);
    Route::post('confirmationofmarriage', 'ConfirmationOfMarriagesController@store')->middleware(['auth:api', 'can:confirmationofmarriage-create']);
    Route::put('confirmationofmarriage/{id}', 'ConfirmationOfMarriagesController@update')->middleware(['auth:api', 'can:confirmationofmarriage-update']);
    Route::delete('confirmationofmarriage/{id}', 'ConfirmationOfMarriagesController@destroy')->middleware(['auth:api', 'can:confirmationofmarriage-delete']);
    Route::get('confirmationofmarriage/{id}/print', 'ConfirmationOfMarriagesController@downloadDocument')->middleware(['auth:api', 'can:confirmationofmarriage-create']);

    Route::get('familycard', 'FamilyCardsController@index')->middleware(['auth:api', 'can:familycard-index']);
    Route::get('familycard/{id}', 'FamilyCardsController@show')->middleware(['auth:api', 'can:familycard-read']);
    Route::post('familycard', 'FamilyCardsController@store')->middleware(['auth:api', 'can:familycard-create']);
    Route::put('familycard/{id}', 'FamilyCardsController@update')->middleware(['auth:api', 'can:familycard-update']);
    Route::delete('familycard/{id}', 'FamilyCardsController@destroy')->middleware(['auth:api', 'can:familycard-delete']);
    Route::get('familycard/{id}/print', 'FamilyCardsController@downloadDocument')->middleware(['auth:api', 'can:familycard-create']);

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

    Route::get('building', 'BuildingsController@index')->middleware(['auth:api','can:building-index']);
    Route::get('building/{id}', 'BuildingsController@show')->middleware(['auth:api','can:building-read']);
    Route::post('building', 'BuildingsController@store')->middleware(['auth:api','can:building-create']);
    Route::put('building/{id}', 'BuildingsController@update')->middleware(['auth:api','can:building-update']);
    Route::delete('building/{id}', 'BuildingsController@destroy')->middleware(['auth:api','can:building-delete']);

    Route::get('room', 'RoomsController@index')->middleware(['auth:api', 'can:room-index']);
    Route::get('room/{id}', 'RoomsController@show')->middleware(['auth:api', 'can:room-read']);
    Route::post('room', 'RoomsController@store')->middleware(['auth:api', 'can:room-create']);
    Route::put('room/{id}', 'RoomsController@update')->middleware(['auth:api', 'can:room-update']);
    Route::delete('room/{id}', 'RoomsController@destroy')->middleware(['auth:api', 'can:room-delete']);

    Route::get('itemtype', 'ItemTypesController@index')->middleware(['auth:api', 'can:itemtype-index']);
    Route::get('itemtype/{id}', 'ItemTypesController@show')->middleware(['auth:api', 'can:itemtype-read']);
    Route::post('itemtype', 'ItemTypesController@store')->middleware(['auth:api', 'can:itemtype-create']);
    Route::put('itemtype/{id}', 'ItemTypesController@update')->middleware(['auth:api', 'can:itemtype-update']);
    Route::delete('itemtype/{id}', 'ItemTypesController@destroy')->middleware(['auth:api', 'can:itemtype-delete']);

    route::get('item', 'ItemsController@index')->middleware(['auth:api', 'can:item-index']);
    route::get('item/{id}', 'ItemsController@show')->middleware(['auth:api', 'can:item-read']);
    route::post('item', 'ItemsController@store')->middleware(['auth:api', 'can:item-create']);
    route::put('item/{id}', 'ItemsController@update')->middleware(['auth:api', 'can:item-update']);
    route::delete('item/{id}', 'ItemsController@destroy')->middleware(['auth:api', 'can:item-delete']);

    Route::get('itemstatus', 'ItemStatusesController@index')->middleware(['auth:api', 'can:itemstatus-index']);
    Route::get('itemstatus/{id}', 'ItemStatusesController@show')->middleware(['auth:api', 'can:itemstatus-read']);
    Route::post('itemstatus', 'ItemStatusesController@store')->middleware(['auth:api', 'can:itemstatus-create']);
    Route::put('itemstatus/{id}', 'ItemStatusesController@update')->middleware(['auth:api', 'can:itemstatus-update']);
    Route::delete('itemstatus/{id}', 'ItemStatusesController@destroy')->middleware(['auth:api', 'can:itemstatus-delete']);

    Route::get('finance', 'FinancesController@index')->middleware(['auth:api', 'can:finance-index']);
    Route::get('finance/{id}', 'FinancesController@show')->middleware(['auth:api', 'can:finance-read']);
    Route::get('finance/add/lastBalance', 'FinancesController@lastBalance')->middleware(['auth:api', 'can:finance-read']);
    Route::post('finance', 'FinancesController@store')->middleware(['auth:api', 'can:finance-create']);
    Route::put('finance/{id}', 'FinancesController@update')->middleware(['auth:api', 'can:finance-update']);
    Route::delete('finance/{id}', 'FinancesController@destroy')->middleware(['auth:api', 'can:finance-delete']);
    Route::delete('finance','FinancesController@destroyAll')->middleware(['auth:api', 'can:finance-delete']);

    Route::get('bookingroom', 'BookingRoomsController@index')->middleware(['auth:api', 'can:bookingroom-index']);
    Route::get('bookingroom/{id}', 'BookingRoomsController@show')->middleware(['auth:api', 'can:bookingroom-read']);
    Route::post('bookingroom', 'BookingRoomsController@store')->middleware(['auth:api', 'can:bookingroom-create']);
    Route::put('bookingroom/{id}', 'BookingRoomsController@update')->middleware(['auth:api', 'can:bookingroom-update']);
    Route::delete('bookingroom/{id}', 'BookingRoomsController@destroy')->middleware(['auth:api', 'can:bookingroom-delete']);
    Route::delete('bookingroom','BookingRoomsController@destroyAll')->middleware(['auth:api', 'can:bookingroom-delete']);
});

Route::get('select/branchWeb', 'BranchesController@select');
Route::post('formdatajemaat', 'WebUsersController@store');
// Route::get('webfamilycard', 'WebFamilyCardsController@index');
