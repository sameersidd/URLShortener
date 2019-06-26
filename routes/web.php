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

Route::get('/', route('home'));

Route::get('/home', 'URLController@index')->name('home');
Route::post('/urlshorten', 'URLController@saveURL')->name('save');
Route::get('/url', 'URLController@index')->name('url');
Route::get('/{key}', 'URLController@redirectURL')->name('redirect');
Route::post('/api/register', 'URLController@register')->name('apiRegister');
