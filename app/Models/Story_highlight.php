<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story_highlight extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'story_id',
        'title',
        'cover_pic'
    ];

    protected $primary_key = "highlight_id";

    protected $table = "story_highlight";
}
