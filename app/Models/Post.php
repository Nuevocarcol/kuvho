<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
  use HasFactory;

  protected $table = 'posts';
  protected $primaryKey = "post_id";
  protected $guard = 'admin';
  protected $fillable = [
    'user_id',
    'text',
    'image',
    'video',
    'video_thumbnail',
    'location',
    'create_date'
  ];
  
  protected $casts = [
        'post_id' => 'string',
    ];

  public function user()
  {
    return $this->hasOne('App\Models\User', 'id', 'user_id')->select('id', 'username', 'profile_pic')->withDefault();
  }

  public function comment()
  {
    return $this->hasMany('App\Models\Comment', 'post_id');
  }

  public function posts()
  {
    return $this->hasMany(Comment::class, 'post_id');
  }
}
