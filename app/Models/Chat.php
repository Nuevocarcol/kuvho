<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_user',
        'to_user',
        'message',
        'url',
        'date',
        'time',
        'send_post',
        'send_story',
        'read_message',
        'type',
        
    ];

}
