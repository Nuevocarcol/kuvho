<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_report extends Model
{
  use HasFactory;

  protected $table = 'users_report';
  protected $guard = 'admin';
  protected $fillable = ['reportByUserId', 'reportedUserId', 'status', 'report_text', 'create_date'];
}
