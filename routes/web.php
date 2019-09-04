<?php

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

Route::get('/', 'HomeController@home')->name('home');
Auth::routes();

//-----Teams----------------------------------------------------------------------------------------------------------//

Route::get('/teams', 'TeamsController@index')->name('teams.index');
Route::get('/teams/create', 'TeamsController@create')->name('teams.create');
Route::post('/teams', 'TeamsController@store')->name('teams.store');
Route::get('/teams/{team}/edit', 'TeamsController@edit')->name('teams.edit');
Route::put('/teams/{team}/update', 'TeamsController@update')->name('teams.update');

Route::group(['prefix' => '/teams/{team}', 'middleware' => ['team.member']], function () {

    //-----Contacts---------------------------------------------------------------------------------------------------//

    Route::get('/contacts', 'ContactsController@index')->name('contacts.index');
    Route::get('/contacts/create', 'ContactsController@create')->name('contacts.create');
    Route::get('/contacts', 'ContactsController@store')->name('contacts.store');
    Route::get('/contacts/{contact}', 'ContactsController@show')->name('contacts.show');
    Route::put('/contacts/{contact}/edit', 'ContactsController@edit')->name('contacts.edit');
    Route::put('/contacts/{contact}', 'ContactsController@update')->name('contacts.update');
    Route::delete('/contacts/{contact}', 'ContactsController@delete')->name('contacts.delete');
    Route::delete('/contacts', 'ContactsController@deleteMultiple')->name('contacts.deleteMultiple');

    //-----Webhooks-----------------------------------------------------------------------------------------------//

    Route::get('/webhooks', 'WebhooksController@index');
    Route::get('/webhooks/types', 'WebhooksController@types');
    Route::get('/webhooks/logs', 'WebhooksController@logs');
    Route::post('/webhooks', 'WebhooksController@store');
    Route::put('/webhooks/{webhook}', 'WebhooksController@update');
    Route::delete('/webhooks/{webhook}', 'WebhooksController@delete');

});

