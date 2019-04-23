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



$api = app('Dingo\Api\Routing\Router');

$api->version('v1',['prefix'=>"api/v1",'namespace'=>'App\Http\Controllers\V1'],function($api){

    $api->any('/user/{action}',     "UserController@action");

    $api->any('/category/{action}', "CategoryController@action");

    $api->any('/question/{action}', "QuestionController@action");

    $api->any('/chart/{action}',    "ChartController@action");

});