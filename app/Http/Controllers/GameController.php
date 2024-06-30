<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class GameController extends Controller
{
  /**
   * @lrd:start
   * # Admin Index
   * Display a listing of all games grouped by player for the admin.
   *
   * @return JsonResponse List of all games grouped by player.
   * @lrd:end
   */
  public function adminIndex(): JsonResponse
  {
    Gate::authorize('viewAny', User::class);
    $gamesGroupedByPlayer = Game::getGamesGroupedByPlayer();

    if (empty($gamesGroupedByPlayer)) {
      return response()->json(['message' => 'No games found'], 404);
    }

    return response()->json($gamesGroupedByPlayer, 200);
  }

  /**
   * @lrd:start
   * # Play Game
   * Store a newly created game in storage.
   *
   * @return JsonResponse Details of the newly created game.
   * @lrd:end
   */
  public function playGame(): JsonResponse
  {
    $newGame = new Game();
    $newGame->user_id = auth()->user()->id;
    $newGame->dice1 = rand(1, 6);
    $newGame->dice2 = rand(1, 6);
    $newGame->result = $newGame->dice1 + $newGame->dice2;
    $newGame->won = $newGame->result == 7;
    $newGame->save();
    return response()->json(GameResource::make($newGame), 201);
  }

  /**
   * @lrd:start
   * # Delete Games
   * Remove all games for the given user.
   *
   * @param User $user User whose games are to be deleted.
   * @return JsonResponse Empty response with status code 204 on success.
   * @lrd:end
   */
  public function destroy(User $user): JsonResponse
  {
    Gate::authorize('delete', $user);
    Game::where('user_id', $user->id)->delete();

    return response()->json(null, 204);
  }
}
