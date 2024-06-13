<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteAllGamesForUserRequest;
use App\Http\Resources\GameGroupedByPlayerResource;
use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class GameController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function adminIndex(): JsonResponse
  {
    $games = Game::all();
    return response()->json(GameGroupedByPlayerResource::collection($games), 200);
  }

  public function playerIndex(): JsonResponse
  {
    $user_id = auth()->user()->id;
    $games = Game::where('user_id', $user_id)->get();
    return response()->json(GameResource::collection($games), 200);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function playGame(): JsonResponse
  {
    $newGame = new Game();
    $newGame->user_id = auth()->user()->id;
    $newGame->dice1 = rand(1, 6);
    $newGame->dice2 = rand(1, 6);
    $newGame->result = $newGame->dice1 + $newGame->dice2;
    $newGame->won = $newGame->result >= 7;
    $newGame->save();
    return response()->json(GameResource::make($newGame), 201);
  }

  /**
   * Display the specified resource.
   */
  public function show(Game $game): JsonResponse
  {
    return response()->json(GameResource::make($game));
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(User $user): JsonResponse
  {
    Game::where('user_id', $user->id)->delete();

    return response()->json(null, 204);
  }
}
