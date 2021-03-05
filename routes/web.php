<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


//Route::get('/{name?}/{edad?}', function ($name = null, $edad = null) {
//    return view('welcome', ['name' => $name, 'edad' => $edad]);
//    return view('welcome')->with('name',$name);
//});
Route::get('/api' ,function (){
    return view('index');
});

Route::get('/' ,function (){
    return view('index');
});




