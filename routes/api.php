<?php

use Illuminate\Support\Facades\Route;
use App\Models\GameSession;

Route::get('/status', function () {
    return [
        'status' => 'online',
        'total_games' => GameSession::count(),
        'active_games' => GameSession::where('status', 'active')->count(),
    ];
});
