<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected  $guarded =  [];

    protected $casts = [
        'free_open_ai_items' => 'array'
    ];
}
