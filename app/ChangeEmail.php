<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class ChangeEmail extends Model
{
    protected $dates = [
        'created_at', 'updated_at',
    ];

    protected $fillable = [
        'user_id',
        'token',
        'email',
    ];
}