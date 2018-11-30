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

//登录相关
Route::middleware('cors')->any('/user/login', 'Api\UserController@login')->name('login');
Route::middleware('cors')->any('/user/info', 'Api\UserController@info')->name('info');


//权限管理
Route::middleware('cors')->any('/permission/userList', 'Api\PermissionController@userList')->name('userList');
Route::middleware('cors')->any('/permission/updateUser', 'Api\PermissionController@updateUser')->name('updateUser');
Route::middleware('cors')->any('/permission/createUser', 'Api\PermissionController@createUser')->name('createUser');
Route::middleware('cors')->any('/permission/deleteUser', 'Api\PermissionController@deleteUser')->name('deleteUser');

Route::middleware('cors')->any('/permission/roleList', 'Api\PermissionController@roleList')->name('roleList');
Route::middleware('cors')->any('/permission/updateRole', 'Api\PermissionController@updateRole')->name('updateRole');
Route::middleware('cors')->any('/permission/createRole', 'Api\PermissionController@createRole')->name('createRole');
Route::middleware('cors')->any('/permission/deleteRole', 'Api\PermissionController@deleteRole')->name('deleteRole');

Route::middleware('cors')->any('/permission/menuList', 'Api\PermissionController@menuList')->name('menuList');
Route::middleware('cors')->any('/permission/updateMenu', 'Api\PermissionController@updateMenu')->name('updateMenu');
Route::middleware('cors')->any('/permission/createMenu', 'Api\PermissionController@createMenu')->name('createMenu');
Route::middleware('cors')->any('/permission/deleteMenu', 'Api\PermissionController@deleteMenu')->name('deleteMenu');

use App\User;
use App\Http\Resources\UserResource;

Route::get('/test1', function () {
    return new UserResource(User::find(1));
});

use App\Http\Resources\UserCollection;
Route::get('/test2', function () {
    return new UserCollection(User::all());
});
