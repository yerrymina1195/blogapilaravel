<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\ArticleController;
use App\Http\Controllers\Api\V2\AuthController;
use App\Http\Controllers\Api\V2\CategorieController;
use App\Http\Controllers\Api\V2\CommentController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::post('/updateProfilUser', [AuthController::class, 'updateUserProfil']);
});




Route::group(['prefix' => 'article'], function () {
    Route::controller(ArticleController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('show/{id}', 'show');
        Route::middleware('auth')->group(function () {
            Route::post('store', 'store');
            Route::delete('delete_article/{id}', 'delete_article');
            Route::put('update_article/{id}', 'update_article');
        });
    });
});


Route::group(['prefix' => 'category'], function () {
    Route::controller(CategorieController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('show/{id}', 'show');
        Route::middleware('auth')->group(function () {
            Route::post('store', 'store');
            Route::put('update_category/{id}','update_category');
            Route::delete('delete_category/{id}', 'delete_article');
        });
    });
});


Route::group(['prefix'=>'comment'],function(){

    Route::controller(CommentController::class)->group(function(){
        Route::get('/','index');
        Route::get('show/{id}', 'show');
        
        Route::middleware('auth')->group(function () {
            Route::post('store','store');
            Route::put('update_comment/{id}','update_comment');
            Route::delete('delete_comment/{id}','delete_comment');
        });
    });
});


