<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class post_user_tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'tag_users',
        'created_date'
    ];

    protected $primary_key = "tag_id";
}
