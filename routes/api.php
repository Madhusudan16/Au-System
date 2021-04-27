<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

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

Route::group(['prefix'=>"v1",'guard'=>'api'],function(){
    Route::group(['prefix'=>'auth'], function(){
        //Login
        Route::post("/login",[UserController::class,'login']);
        Route::post("/join/{id}",[UserController::class,'join'])->name("join");
        Route::post("/account/verify",[UserController::class,'verify']);

    });

    Route::group(['middleware' => 'auth:api'], function(){
        Route::group(['prefix'=>'invite'], function() {
            Route::post("/",[UserController::class,'inviteUser']);
        });
        Route::group(['prefix'=>'account'], function() {
            Route::post("/update",[UserController::class,'updateProfile']);
        });
        Route::group(['prefix'=>'auth'], function() {
            Route::get("/logout",[UserController::class,'logout']);
        });
    });
});
