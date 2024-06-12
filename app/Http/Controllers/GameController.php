<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Http\Resources\GameGroupedByPlayerResource;
use App\Http\Resources\GameResource;
use App\Models\Game;
use Illuminate\Http\JsonResponse;

class GameController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(): JsonResponse
  {
    $games = Game::all();
    return response()->json(GameGroupedByPlayerResource::collection($games));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(StoreGameRequest $request)
  {
    //
  }

  /**
   * Display the specified resource.
   */
  public function show(Game $game)
  {
    return response()->json(GameResource::make($game));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UpdateGameRequest $request, Game $game)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Game $game)
  {
    //
  }
}
