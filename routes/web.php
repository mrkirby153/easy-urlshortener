<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'UrlController@index');

Auth::routes();

Route::post('/url/create', 'UrlController@create');
Route::get('/url/get', 'UrlController@all');
Route::post('/url/delete', 'UrlController@delete');

Route::get('/admin', 'AdminController@index');
Route::get('/admin/status', 'AdminController@status');
Route::get('/admin/urls', 'AdminController@urls');

Route::get('{url}', 'UrlController@click');