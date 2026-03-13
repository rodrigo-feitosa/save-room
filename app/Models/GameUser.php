<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
