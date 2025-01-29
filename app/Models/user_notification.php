<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_user',
        'to_user',
        'post_id',
        'title',
        'message',
        'read_status',
        'requests_status',
        'not_type',
        'date',
        'reel_id'
    ];

    protected $table = "user_notification";

    protected $primary_key = "not_id";
}
