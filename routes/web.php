<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GameController;

Route::get('/', function () { return view('welcome'); }); // Your landing page
Route::get('/game/start', [GameController::class, 'start'])->name('game.start');
Route::get('/game/{id}', [GameController::class, 'show'])->name('game.show');
Route::post('/game/{id}/play/{index}', [GameController::class, 'playCard'])->name('game.play');
Route::post('/game/{id}/flee', [GameController::class, 'flee'])->name('game.flee');
Route::post('/game/{id}/next-room', [GameController::class, 'nextRoom'])->name('game.nextRoom');
