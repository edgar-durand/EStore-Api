<?php

use App\Http\Controllers\UserController;
use App\User;
use Illuminate\Support\Facades\Route;


//Route::get('/{name?}/{edad?}', function ($name = null, $edad = null) {
//    return view('welcome', ['name' => $name, 'edad' => $edad]);
//    return view('welcome')->with('name',$name);
//});
Route::get('/api' ,function (){
    $users = User::all(['id','username','First_name','last_name','email'])->splice(0,5);
    return view('index',['users'=>$users]);
});

Route::get('/' ,function (){
    $users = User::all(['id','username','First_name','last_name','email'])->splice(0,5);
    return view('index',['users'=>$users]);
});




