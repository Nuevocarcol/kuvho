<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
  use HasFactory;

  protected $table = "story";
  protected $primaryKey = "story_id";
  protected $guard = 'admin';
  protected $fillable = [
    'user_id',
    'url',
    'type',
    'create_date',
    'is_delete',
    'story_seen'
  ];

  public function user()
  {
    return $this->hasOne('App\Models\User', 'id', 'user_id')->select('id', 'username', 'profile_pic')->withDefault();
  }
}
