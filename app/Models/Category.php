<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'user_id'];

    // Define any relationships or additional methods here if needed
}
