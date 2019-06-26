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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('{key}/urls', 'URLController@apiView')->name('apiView');
Route::put('{key}/urls', 'URLController@APIsaveURL')->name('apiSave');
Route::post('{key}/urls', function () {
    return response()->json([
        'error' => 'Only GET and PUT methods supported'
    ], 400);
});
