<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('players')->group(function () {
  Route::post('login', [UserController::class, 'login'])->name('login');
  Route::post('', [UserController::class, 'store'])->name('store');
  Route::apiResource('/', UserController::class)->middleware('auth:api')->except('store', 'destroy');
  Route::post('games', [GameController::class, 'playGame'])->name('playGame')->middleware('auth:api');
  Route::get('games', [GameController::class, 'playerIndex'])->name('playerIndex')->middleware('auth:api');
  Route::get('games/admin', [GameController::class, 'adminIndex'])->name('adminIndex')->middleware('auth:api');
  Route::get('{user}/games', [GameController::class, 'show'])->name('showPlayerGames')->middleware('auth:api');
  Route::delete('{user}/games', [GameController::class, 'destroy'])->name('destroyPlayerGames')->middleware('auth:api');
  Route::get('ranking', [UserController::class, 'ranking'])->name('rankingAllPlayers')->middleware('auth:api');
  Route::get('ranking/winner', [UserController::class, 'bestPlayer'])->name('rankingBestPlayer')->middleware('auth:api');
  Route::get('ranking/loser', [UserController::class, 'worstPlayer'])->name('rankingWorstPlayer')->middleware('auth:api');
});
