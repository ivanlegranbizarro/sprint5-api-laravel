<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Models\User;
use App\Services\StatisticsService;
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
   * # Player Index
   * Display a listing of games for the authenticated user and calculate their success percentage.
   *
   * @param StatisticsService $statistics Service to calculate statistics.
   * @return JsonResponse List of games for the authenticated user and their success percentage.
   * @lrd:end
   */
  public function playerIndex(StatisticsService $statistics): JsonResponse
  {
    $user_id = auth()->user()->id;
    $games = Game::where('user_id', $user_id)->get();
    $your_statistics = $statistics->calculateSuccessPercentage($games->toArray());
    return response()->json([
      'games' => GameResource::collection($games),
      'success_percentage' => $your_statistics
    ]);
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
    $newGame->won = $newGame->result >= 7;
    $newGame->save();
    return response()->json(GameResource::make($newGame), 201);
  }

  /**
   * @lrd:start
   * # Show Games
   * Display a list of games for the specified user.
   *
   * @param User $user User whose games are to be retrieved.
   * @return JsonResponse List of games for the specified user.
   * @lrd:end
   */
  public function show(User $user): JsonResponse
  {
    $games = Game::where('user_id', $user->id)->get();
    return response()->json(GameResource::collection($games), 200);
  }

  /**
   * @lrd:start
   * # Delete Games
   * Remove all games of the authenticated user.
   *
   * @param User $user User whose games are to be deleted.
   * @return JsonResponse Empty response with status code 204 on success.
   * @lrd:end
   */
  public function destroy(): JsonResponse
  {
    $user = auth()->user();
    Gate::authorize('delete', $user);
    Game::where('user_id', $user->id)->delete();

    return response()->json(null, 204);
  }
}
