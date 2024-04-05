<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


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




// Route::group(['prefix'=>'category'],function($router){

//     Route::controller(CategorieController::class)->group(function(){
//         Route::get('/','index');
//         Route::get('show/{id}', 'show');
//         Route::post('store','store');
//         Route::put('update_category/{id}','update_category');
//         Route::delete('delete_category/{id}','delete_category');
//     });
// });
// Route::group(['prefix'=>'article'],function($router){

//     Route::controller(ArticleController::class)->group(function(){
//         Route::get('/','index');
//         Route::get('show/{id}', 'show');
//         Route::post('store','store');
//         Route::put('update_article/{id}','update_article');
//         Route::delete('delete_article/{id}','delete_article');
//     });
// });