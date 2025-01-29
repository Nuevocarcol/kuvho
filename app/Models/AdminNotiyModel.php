<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotiyModel extends Model
{
    use HasFactory;
    protected $table = 'admin_notifications';
    protected $fillable = ['user_id', 'title', 'message', 'image','date'];

}
