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

Route::middleware('auth:api')->get('/test',function (){
    return 'test';
});


Route::middleware('cors')->any('/user/login', 'Api\UserController@login')->name('login');

use App\User;
use App\Http\Resources\UserResource;

Route::get('/test1', function () {
    return new UserResource(User::find(1));
});

use App\Http\Resources\UserCollection;
Route::get('/test2', function () {
    return new UserCollection(User::all());
});
