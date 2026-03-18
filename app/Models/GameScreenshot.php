<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameScreenshot extends Model
{
    protected $table = 'game_screenshots';

    protected $fillable = [
        'game_id',
        'screenshot',
    ];

    public $timestamps = false;
}
