<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

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
###>> Purchase <<###

Route::group(['prefix' => 'google'], function () {
    Route::post('test', function () {
        \App\Jobs\started::dispatch("gökhan");
        return ["a"];
    });
    Route::get('/purchase/{receipt}', 'Api\Purchase\GoogleController@check');
});

Route::group(['prefix' => 'ios'], function () {
    Route::post('test', function () {
        \App\Jobs\started::dispatch("gökhan");
        return ["a"];
    });
    Route::get('/purchase/{receipt}', 'Api\Purchase\IosController@check');
});

## USER ##
Route::group(['prefix' => 'auth'], function () {
    Route::middleware('ClientToken')->group(function () {
        Route::get('/user', "Api\UserController@index");
    });
    Route::post('/user', "Api\UserController@store");
});

## SUBSCRIPTION ##
Route::group(['prefix' => 'subscription'], function () {
    Route::middleware('ClientToken')->group(function () {
        Route::get('/check', "Api\SubscriptionController@check");
        Route::post('/create',"Api\SubscriptionController@create");
    });
});

Route::post('/3rd-party', function () {
    return response()->json([
        'status' => 'OK',
    ], Response::HTTP_CREATED);
});


