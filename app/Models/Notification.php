<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications'; // Replace 'table_name' with the name of your database table

    protected $fillable = [
        'from_user',
        'to_user',
        'post_id',
        'title',
        'message',
        'date',
        'tokenid',
        'channelname',
        'call_type',
        'is_type',
        
        // Add more columns as needed
    ];
}
