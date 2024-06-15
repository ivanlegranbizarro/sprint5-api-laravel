<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('players')->group(function () {
  Route::post('login', [UserController::class, 'login'])->name('login');
  Route::post('', [UserController::class, 'store'])->name('store');
  Route::apiResource('/', UserController::class)
    ->middleware('auth:api')
    ->except('store', 'destroy');

  Route::middleware('auth:api')->group(function () {
    Route::prefix('games')->group(function () {
      Route::post('/', [GameController::class, 'playGame'])->name('playGame');
      Route::get('/', [GameController::class, 'playerIndex'])->name('playerIndex');
      Route::get('admin', [GameController::class, 'adminIndex'])->name('adminIndex');
      Route::get('{user}', [GameController::class, 'show'])->name('showPlayerGames');
      Route::delete('{user}', [GameController::class, 'destroy'])->name('destroyPlayerGames');
    });

    Route::middleware('auth:api')->group(function () {
      Route::prefix('ranking')->group(function () {
        Route::get('/', [UserController::class, 'ranking'])->name('rankingAllPlayers');
        Route::get('winner', [UserController::class, 'bestPlayer'])->name('rankingBestPlayer');
        Route::get('loser', [UserController::class, 'worstPlayer'])->name('rankingWorstPlayer');
      });
    });
  });
});
