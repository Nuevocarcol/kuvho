<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
// use Laravel\Passport\HasApiTokens;

class PrivacyModel extends Authenticatable
{
    use Notifiable;

    // protected $primaryKey = 'ID';
    protected $table = 'privacy_policy';
    protected $fillable = ['privacy_policy', 'term_conditions', 'notify_key', 'api_url'];
    // protected $guarded = array();
}