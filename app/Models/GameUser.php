<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameUser extends Model
{
    protected $table = "user_games";

    protected $fillable = [
        'user_id',
        'game_id',
        'status',
        'title',
        'description',
        'cover',
        'released_date',
        'metacritic_score',
        'developers',
        'publisher',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
