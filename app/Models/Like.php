<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
  use HasFactory;

  protected $table = "likes";
  protected $guard = 'admin';
  protected $primary_key = "like_id";
  protected $fillable = [
    'user_id',
    'post_id',
    'date'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
