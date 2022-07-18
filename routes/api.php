<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\API\CityController;
use App\Http\Controllers\API\CouncilVoteController;
use App\Http\Controllers\API\CouncilSessionItemController;
use App\Http\Controllers\API\CouncilVotesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\CouncilController;
use App\Http\Controllers\API\AccountsController;
use Illuminate\Http\Request;

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

Route::get('cities',  [CityController::class, 'index_all'])->name('city.all');

Route::post('GET_Council_Sessions', [CouncilController::class, 'index']);
Route::post('UPDATE_Council_Session', [CouncilController::class, 'update']);
Route::post('POST_Council_Session', [CouncilController::class, 'store']);
Route::post('GET_Voting_Users', [AccountsController::class, 'getUsers']);
Route::post('GET_Votes', [CouncilVotesController::class, 'getVotes']);
Route::post('POST_Vote', [CouncilVoteController::class, 'store']);
Route::post('UPDATE_Council_Session_Item', [CouncilSessionItemController::class, 'update']);
Route::post('POST_Session_Item', [CouncilSessionItemController::class, 'store']);
Route::post('POST_Notification', [NotificationController::class, 'store']);
Route::post('GET_Notification', [NotificationController::class, 'getLast']);




Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [LoginController::class, 'logout']);



   /* Route::patch('settings/profile', [ProfileController::class, 'update']);
    Route::patch('settings/password', [PasswordController::class, 'update']);
    Route::patch('admin/user/{user}/reset_password', [PasswordController::class, 'reset_password']);
    Route::patch('admin/user/{user}/permissions', [UC::class, 'permissions']);

    Route::resource('city',CityController::class);
    Route::resource('permission',PermissionController::class);
    Route::resource('role',RoleController::class);
    Route::resource('admin/user',UC::class);
    Route::resource('group',GroupCOntroller::class);
    Route::resource('marker',MarkerController::class);*/

    //get all items
   /* Route::get('permissions',  [PermissionController::class, 'index_all'])->name('permission.all');
    Route::get('roles',  [RoleController::class, 'index_all'])->name('role.all');
    Route::get('groups',  [GroupCOntroller::class, 'index_all'])->name('group.all');
    Route::get('markers',  [MarkerController::class, 'index_all'])->name('marker.all');*/

    //post method
  //  Route::post('markers-group',  [MarkerController::class, 'mergeMarkersWithGroup'])->name('markersGroupMarge');

    //DOWNLOAD
   //  Route::get('group/{group}/export-marker-picture',  [ExportController::class, 'generatePDF'])->name('generatePDF');
  ///   Route::get('marker/{marker}/export-marker-text',  [ExportController::class, 'generatePdfWithText'])->name('generatePdfWithText');

     //check name marker - CLONE marker
     //Route::get('marker/check-name',  [MarkerController::class, 'checkName'])->name('marker.checkName');

    //check name marker - CLONE marker
   // Route::get('check-name',  [MarkerController::class, 'checkName'])->name('marker.checkName');
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [RegisterController::class, 'register']);

    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset']);

    Route::post('email/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend']);

    Route::post('oauth/{driver}', [OAuthController::class, 'redirect']);
    Route::get('oauth/{driver}/callback', [OAuthController::class, 'handleCallback'])->name('oauth.callback');
});
