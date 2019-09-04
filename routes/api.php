<?php

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

Route::group(['middleware' => 'auth:api'], function () {
    //-----Teams----------------------------------------------------------------------------------------------------------//

    Route::get('/teams', 'TeamsController@index');
    Route::post('/teams', 'TeamsController@store');
    Route::put('/teams/{team}/update', 'TeamsController@update');

    //-----Users------------------------------------------------------------------------------------------------------//

    Route::get('teams/{team}/user', 'UsersController@index');
    Route::get('/user', 'UsersController@show');

    //-----Contacts---------------------------------------------------------------------------------------------------//

    Route::get('/contacts', 'ContactsController@index');
    Route::get('/contacts', 'ContactsController@store');
    Route::get('/contacts/{contact}', 'ContactsController@show');
    Route::put('/contacts/{contact}', 'ContactsController@update');
    Route::delete('/contacts/{contact}', 'ContactsController@delete');
    Route::delete('/contacts', 'ContactsController@deleteMultiple');

});