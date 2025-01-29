<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'post_pic',
        'video_thumbnail',
        'description',
        'create_date',
        'location',
    ];

    protected $primary_key = "id";

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id')->select('id','username','profile_pic')->withDefault();
    }
    
    public function reels_comment()
    {
        return $this->hasMany('App\Models\Reel_Comment', 'reel_id');
    }
}
