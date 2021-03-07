<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $guarded = [];
    protected $hidden = ['updated_at','created_at','user_id','account_id'];
}
