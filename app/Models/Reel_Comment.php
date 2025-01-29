<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reel_Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reel_id',
        'text',
        'date'
    ];

    protected $primary_key = "reel_comment_id";
     protected $table = "reels_comment";


    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id')->select('id','username','profile_pic')->withDefault();
    }
    
    // public function sub_comment()
    // {
    //     return $this->hasMany('App\Models\sub_comment', 'comment_id', 'comment_id');
    // }
}
