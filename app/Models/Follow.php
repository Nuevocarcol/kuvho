<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
  use HasFactory;

  protected $table = "follow";
  protected $primary_key = "follow_id";
  protected $guard = 'admin';
  protected $fillable = [
    'from_user',
    'to_user',
    'date',
    'status'
  ];

  public function user()
  {
    return $this->belongsTo(User::class, 'to_user', 'id');
  }
}
