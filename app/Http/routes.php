<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/vtweb', 'PagesController@vtweb');

Route::get('/vtdirect', 'PagesController@vtdirect');
Route::post('/vtdirect', 'PagesController@checkout_process');

Route::get('/vt_transaction', 'PagesController@transaction');
Route::post('/vt_transaction', 'PagesController@transaction_process');

Route::post('/vt_notif', 'PagesController@notification');

Route::get('/snap', 'SnapController@snap');
Route::get('/snaptoken', 'SnapController@token');
Route::post('/snapfinish', 'SnapController@finish');
