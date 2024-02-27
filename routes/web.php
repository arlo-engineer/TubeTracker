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

// Route::get('/youtube', 'App\Http\Controllers\YoutubeapiController@index');

Route::get('/youtube/auth', 'App\Http\Controllers\YoutubeapiController@redirectToProvider');
Route::get('/youtube', 'App\Http\Controllers\YoutubeapiController@getVideos');

