<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile_blocklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'blockedByUserId',
        'blockedUserId',   
    ];

    protected $table = "profile_blocklist";
}
