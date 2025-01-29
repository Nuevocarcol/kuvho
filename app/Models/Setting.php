<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
  use HasFactory;
  protected $table = "settings";
  protected $primary_key = "id";
  protected $guard = 'admin';

  protected $fillable = [
    'name',
    'email',
    'text',
    'color',
    'logo',
    'agora_key',
    'notify_key',
    'prv_pol_url',
    'tnc_url'
  ];
}
