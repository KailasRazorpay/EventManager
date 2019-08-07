<?php

use Illuminate\Http\Request;
use App\Mail\Welcome;

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

Route::middleware('auth.basic')->get('/user', function (Request $request) {
    return $request->user();
});

//    View all the events a particular user has been invited to
Route::get('users/events','UsersController@viewEventsOfUser');

//    Allow admin or owner of event to view event invitees
Route::get('events/{events_id}/users','EventsController@viewUsersOfEvent');

//    Allow event owner to invite other users to event
Route::post('events/{id}/invite','EventsController@inviteUsers');

//    Allow user to accept an invitation
//Route::get('/event/{id}/accept','UsersController@accept');

//    Allow users to accept or reject an invitation
Route::patch('/events','UsersController@respondToInvitation');

//    Allow user to reject an invitation
//Route::get('/event/{id}/reject','UsersController@reject');

//    Allow owner or admin to delete event (Should be used only if no one has been invited yet)
Route::get('/event/{id}/cancel','EventsController@cancel');

//    Show all the invitation statuses of a particular event
Route::get('status/{event_id}','StatusesController@showStatusesOfEventInvitation');

//    URIs for resources
Route::apiResource('/users','UsersController');

Route::apiResource('/events','EventsController');

Route::apiResource('/statuses','StatusesController');

