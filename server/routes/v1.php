<?php


//Route::middleware('auth:api')->middleware('userToken')->any('/v1/task/{action}', "V1\TaskController@action");


$api = app('Dingo\Api\Routing\Router');

$api->version('v1',[
    'prefix'=>"api/v1",
    'middleware'=>['userToken'],
    'namespace'=>'App\Http\Controllers\V1'],function($api){

    $api->any('hello',function(){
        return "ok";
    });

    $api->any('/user/{action}',     "UserController@action");

    $api->any('/task/{action}',    "TaskController@action");

});