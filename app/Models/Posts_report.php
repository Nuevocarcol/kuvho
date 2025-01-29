<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts_report extends Model
{
  use HasFactory;

  protected $table = "posts_report";
  protected $guard = 'admin';

  protected $fillable = ['blockedByUserId', 'blockedPostsId', 'report_text'];
}
