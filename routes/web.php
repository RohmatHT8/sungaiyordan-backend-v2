<?php

use App\Http\Controllers\ShdrsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/shdr', 'ShdrsController@test');
Route::get('/baptism', 'BaptismsController@test');
Route::get('/marriage', 'MarriageCertificatesController@test');
Route::get('/confirmationmarriage', 'ConfirmationOfMarriagesController@test');
Route::get('/child', 'ChildSubmissionsController@test');
Route::get('/familycard', 'FamilyCardsController@test');
Route::get('/familycard/{test}', 'FamilyCardsController@downloadDocument');
Route::get('/shdr/{test}', 'ShdrsController@downloadDocument');
Route::get('/baptism/{test}', 'BaptismsController@downloadDocument');
Route::get('/child/{test}', 'ChildSubmissionsController@downloadDocument');
Route::get('/marriage/{test}', 'MarriageCertificatesController@downloadDocument');

//log-viewers
Route::get('log-viewers', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
