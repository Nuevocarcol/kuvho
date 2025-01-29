<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class View_story extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_img_id',
        'user_id',
        'story_seen',
    ];

    protected $table = "view_story";
}
