<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminProfile extends Authenticatable
{
  use HasFactory;
  protected $guard = 'admin';
  protected $table = 'admin';

  protected $fillable = [
    'username',
    'password',
    'first_name',
    'last_name',
    'email',
    'phone',
    'image',
  ];
}
