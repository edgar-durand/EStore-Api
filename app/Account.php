<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = [];
    protected $hidden = ['user_id','active'];
}
