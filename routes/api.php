<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('players')->group(function () {
  Route::post('login', [UserController::class, 'login'])->name('login');
  Route::post('', [UserController::class, 'store'])->name('store');
  Route::apiResource('/', UserController::class)->middleware('auth:api')->except('store', 'destroy');
  Route::post('games', [GameController::class, 'playGame'])->name('playGame')->middleware('auth:api');
  Route::delete('{user}/games', [GameController::class, 'destroy'])->name('destroyPlayerGames')->middleware('auth:api');
});
