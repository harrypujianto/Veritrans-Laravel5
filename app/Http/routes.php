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

Route::get('/vtweb', 'VtwebController@vtweb');

Route::get('/vtdirect', 'VtdirectController@vtdirect');
Route::post('/vtdirect', 'VtdirectController@checkout_process');

Route::get('/vt_transaction', 'TransactionController@transaction');
Route::post('/vt_transaction', 'TransactionController@transaction_process');

Route::post('/vt_notif', 'TransactionController@notification');

Route::get('/snap', 'SnapController@snap');
Route::get('/snaptoken', 'SnapController@token');
Route::post('/snapfinish', 'SnapController@finish');
