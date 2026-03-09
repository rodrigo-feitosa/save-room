<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = 'games';

    protected $fillable = [
        'title',
        'description',
        'release_date',
        'cover',
        'developer',
        'publisher',
        'metacritic_score',
    ];
}
