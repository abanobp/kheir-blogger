<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    // protected $guarded = array('user_id');
    protected $fillable = ['subject' , 'message'];
}