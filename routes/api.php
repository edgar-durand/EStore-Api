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
Route::get('/product/{per_page}','ProductController@index');


Route::group(['middleware' => 'auth:api'], function () {
    Route::apiResource('/user', 'UserController')->except(['index','destroy','store','show','getByPage','userId']);
    Route::get('/user/{per_page}','UserController@getByPage');
    Route::get('/user/get_by_id/{user_id}','UserController@userId');
    Route::get('/profile','UserController@show');
    Route::post('/logout','UserController@logout');

    Route::apiResource('/category', 'CategoryController')->except('show');

    Route::apiResource('/product', 'ProductController')->except(['index','show']);
    Route::get('/product_detail/{product_id}','ProductController@show');
    Route::get('/my_product','ProductController@myProducts');

    Route::apiResource('/concept','ConceptoController');

    Route::apiResource('/account','AccountController');

    Route::post('/purchase','PurchaseController@store');
    Route::post('/purchase/confirm','PurchaseController@confirm');
    Route::post('/purchase/decline','PurchaseController@decline');
    Route::get('/purchase/get_pending','PurchaseController@getPendingPurchase');
    Route::get('/purchase/get_confirmed','PurchaseController@getConfirmedPurchase');
    Route::get('/purchase/get_declined','PurchaseController@getDeclinedPurchase');
    Route::get('/purchase/get_all','PurchaseController@getAllPurchase');
    Route::get('/purchase/sale_request','PurchaseController@saleRequest');
    Route::post('/purchase/{account_id}','PurchaseController@show');

});


