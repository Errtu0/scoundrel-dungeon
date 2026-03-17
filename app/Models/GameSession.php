<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
   // This allows the Controller to save data to these columns
    protected $fillable = [
        'health',
        'deck',
        'current_room',
        'weapon_val',
        'last_slain_val',
        'can_flee',
        'drank_potion'
    ];

    // Don't forget the casts we talked about earlier!
    protected $casts = [
        'deck' => 'array',
        'current_room' => 'array',
    ];
}
