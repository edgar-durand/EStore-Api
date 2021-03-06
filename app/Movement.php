<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
   protected $guarded=[];
   protected $hidden = ['account_id','updated_at','user_id'];
}
