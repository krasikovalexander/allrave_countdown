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

Auth::routes();

Route::get('/', 'HomeController@publicList')->name('public');
Route::get('/home', 'HomeController@index')->name('home');
Route::post('/start', 'HomeController@start')->name('start');
Route::get('/arrived/{event}', 'HomeController@arrived')->name('arrived');
Route::get('/cancel/{event}', 'HomeController@cancel')->name('cancel');
Route::get('/tracking/{event}', 'HomeController@tracking')->name('tracking');
Route::post('/update', 'HomeController@update')->name('update');
Route::get('logout', 'Auth\LoginController@logout')->name('logoutlink');
Route::get('/event/{event}', 'HomeController@viewEvent')->name('event');
Route::post('/arrivings', 'HomeController@arrivings')->name('arrivings');
Route::post('/address', 'HomeController@address')->name('address');
