<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
  use HasFactory;

  protected $table = 'comments';
  protected $guard = 'admin';
  protected $primaryKey = 'comment_id';

  protected $fillable = [
    'user_id',
    'post_id',
    'text',
    'date'
  ];
  
    protected $casts = [
        'comment_id' => 'string',
    ];



  public function user()
  {
    return $this->hasOne('App\Models\User', 'id', 'user_id')->select('id', 'username', 'profile_pic')->withDefault();
  }

  public function sub_comment()
  {
    return $this->hasMany('App\Models\sub_comment', 'comment_id', 'comment_id');
  }

  public function post()
  {
    return $this->belongsTo(Post::class, 'post_id');
  }
}
