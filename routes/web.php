<?php

use Illuminate\Support\Facades\Route;

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

\Illuminate\Support\Facades\Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/users', 'UsersController@index')->name('users.index');
Route::get('/test/{db}/{env}', 'CollectionController@testView')->name('test.index');
Route::get('/collections/{db}/{env}/{table}', 'CollectionController@index')->name('test.index');

Route::get('/init', 'CollectionController@sogInit');
