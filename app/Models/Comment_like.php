<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment_like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'comment_id',
        'post_id',
        'date',
    ];

    protected $primary_key = "c_like_id";
}
