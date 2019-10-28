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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/add', 'HomeController@add')->name('add');


Route::post('/domain/create', 'DomainController@create');
Route::post('/domain/confirm', 'DomainController@confirm');
Route::get('/domain/show', 'DomainController@show');
Route::delete('/domain/delete/{id}', 'DomainController@delete');
