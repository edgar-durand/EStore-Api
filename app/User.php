<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;

use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;


/**
 * @property string password
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;
    protected $fillable = ['email', 'password', 'status_message', 'instagram', 'twitter', 'facebook', 'phone', 'province', 'municipality', 'between', 'number', 'building', 'street', 'photo', 'birth_date', 'last_name', 'first_name', 'username'];
    protected $hidden = ['created_at', 'updated_at', 'email_verified_at', 'remember_token', 'password', 'api_token'];

//    function product()
////    {
////        return $this->belongsToMany(Product::class)->withTimestamps();
////    }
////    function assignProduct($product){
////        $this->product()->sync($product,false);
////    }
////    function getProducts(){
////        return $this->product->flatten()->pluck('name')->unique();
////    }
}
