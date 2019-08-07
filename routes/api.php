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

Route::get('users/events','UsersController@userevents');

Route::get('events/{events_id}/users','EventsController@eventusers');

Route::post('events/{id}/invite','EventsController@invite');

Route::get('/event/{id}/accept','UsersController@accept');

Route::get('/event/{id}/reject','UsersController@reject');

Route::get('/event/{id}/cancel','EventsController@cancel');

Route::apiResource('/users','UsersController');

Route::apiResource('/events','EventsController');

Route::apiResource('/statuses','StatusesController');

Route::get('status/{event_id}','StatusesController@showeventstatus');

