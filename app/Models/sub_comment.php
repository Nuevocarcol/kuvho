<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sub_comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'user_id',
        'text',
        'date'
    ];

    protected $primary_key = "sub_comment_id";

    protected $table = "sub_comment";

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id')->select('id','username','profile_pic')->withDefault();
    }

}
