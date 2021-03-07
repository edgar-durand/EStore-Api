<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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



Route::post('/login','UserController@login');
Route::post('/user','UserController@store');
Route::get('/user','UserController@index');
Route::get('/product','ProductController@index');


Route::group(['middleware' => 'auth:api'], function () {
    Route::apiResource('/user', 'UserController')->except(['index','destroy','store','show']);
    Route::get('/profile','UserController@show');
    Route::apiResource('/category', 'CategoryController')->except('show');
    Route::apiResource('/product', 'ProductController')->except('index');
    Route::get('/my_product','ProductController@myProducts');
    Route::apiResource('/concept','ConceptoController');
    Route::apiResource('/account','AccountController');
    Route::post('/purchase','PurchaseController@store');
    Route::post('/purchase/{account_id}','PurchaseController@show');
    Route::post('/logout','UserController@logout');
});


