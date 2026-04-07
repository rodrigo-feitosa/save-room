<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameUserPlatform extends Model
{
    protected $table = 'game_user_platforms';

    protected $fillable = [
        'game_user_id',
        'platform'
    ];

    public $timestamps = true;
}
