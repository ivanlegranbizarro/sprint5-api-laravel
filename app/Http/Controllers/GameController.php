<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteAllGamesForUserRequest;
use App\Http\Resources\GameGroupedByPlayerResource;
use App\Http\Resources\GameResource;
use App\Models\Game;
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
  public function store()
  {
    $newGame = new Game();
    $user_id = auth()->user()->id;
    $newGame->user_id = $user_id;
    $dice1 = rand(1, 6);
    $dice2 = rand(1, 6);
    $dice1 + $dice2 >= 7 ? $newGame->won = true : $newGame->won = false;
    $newGame->result = $dice1 + $dice2;
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
  public function destroy(DeleteAllGamesForUserRequest $request): JsonResponse
  {
    $data = $request->validated();

    Game::where('user_id', $data['user_id'])->delete();

    return response()->json(null, 204);
  }
}
