<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reel_Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reel_id',
        'date'
    ];

    protected $primary_key = "reel_like_id";
    
    protected $table = "reel_like";
}
