<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('players')->group(function () {
  Route::post('login', [UserController::class, 'login'])->name('login');
  Route::post('', [UserController::class, 'store'])->name('store');
  Route::apiResource('/', UserController::class)->middleware('auth:api')->except('store');
});
