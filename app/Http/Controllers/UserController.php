<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateNicknameRequest;
use App\Http\Resources\UserIndexResource;
use App\Http\Resources\UserShowResource;
use App\Models\User;
use App\Services\StatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
  /**
   * @lrd:start
   * # User Index
   * Display a listing of all users.
   *
   * @param StatisticsService $statistics Service to calculate statistics.
   * @return JsonResponse List of all users with their success percentage.
   * @lrd:end
   */
  public function index(StatisticsService $statistics): JsonResponse
  {
    Gate::authorize('viewAny', User::class);
    $users = User::where('role', 'user')->get();
    foreach ($users as $user) {
      $user->success_percentage = $statistics->calculateSuccessPercentage($user->games->toArray());
    }
    return response()->json(UserIndexResource::collection($users), 200);
  }

  /**
   * @lrd:start
   * # Store User
   * Store a newly created user in storage.
   *
   * @param StoreUserRequest $request Request object containing the user data.
   * @return JsonResponse Message and authentication token for the newly created user.
   * @lrd:end
   */
  public function store(StoreUserRequest $request): JsonResponse
  {
    $data = $request->validated();
    $data['password'] = bcrypt($data['password']);
    $user = User::create($data);

    $token = $user->createToken('auth_token')->accessToken;

    return response()->json(['message' => 'User created successfully', 'token' => $token], 201);
  }

  /**
   * @lrd:start
   * # User Login
   * Login the specified user.
   *
   * @param LoginRequest $request Request object containing the login data.
   * @return JsonResponse Authentication token if login is successful, or error message if not.
   * @lrd:end
   */
  public function login(LoginRequest $request): JsonResponse
  {
    $data = $request->validated();
    if (!auth()->attempt($data)) {
      return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = auth()->user()->createToken('auth_token')->accessToken;

    return response()->json(['token' => $token], 200);
  }

  /**
   * @lrd:start
   * # Show User
   * Display the specified user.
   *
   * @param User $user User to be displayed.
   * @return JsonResponse User details.
   * @lrd:end
   */
  public function show(User $user): JsonResponse
  {
    Gate::authorize('view', $user);
    return response()->json(UserShowResource::make($user), 200);
  }

  /**
   * @lrd:start
   * # Update User Nickname
   * Update the specified user's nickname.
   *
   * @param UpdateNicknameRequest $request Request object containing the new nickname.
   * @param User $user User whose nickname is to be updated.
   * @return JsonResponse Message confirming successful update.
   * @lrd:end
   */
  public function update(UpdateNicknameRequest $request, User $user): JsonResponse
  {
    Gate::authorize('update', $user);
    $data = $request->validated();
    $user->update($data);

    return response()->json(['message' => 'Nickname updated successfully'], 200);
  }

  /**
   * @lrd:start
   * # User Ranking
   * Display a ranking of all users based on their success percentage.
   *
   * @param StatisticsService $statistics Service to calculate statistics.
   * @return JsonResponse Ranking of all users.
   * @lrd:end
   */
  public function ranking(StatisticsService $statistics): JsonResponse
  {
    Gate::authorize('ranking', User::class);
    $users = User::where('role', 'user')->get();
    $rankedUsers = $statistics->rankingAllPlayers($users);

    return response()->json($rankedUsers, 200);
  }

  /**
   * @lrd:start
   * # Best Player
   * Display the user with the highest success percentage.
   *
   * @param StatisticsService $statistics Service to calculate statistics.
   * @return JsonResponse Details of the best player.
   * @lrd:end
   */
  public function bestPlayer(StatisticsService $statistics): JsonResponse
  {
    Gate::authorize('bestPlayer', User::class);
    $users = User::where('role', 'user')->get();
    $bestPlayer = $statistics->rankingBestPlayer($users);

    return response()->json($bestPlayer, 200);
  }

  /**
   * @lrd:start
   * # Worst Player
   * Display the user with the lowest success percentage.
   *
   * @param StatisticsService $statistics Service to calculate statistics.
   * @return JsonResponse Details of the worst player.
   * @lrd:end
   */
  public function worstPlayer(StatisticsService $statistics): JsonResponse
  {
    Gate::authorize('worstPlayer', User::class);
    $users = User::where('role', 'user')->get();
    $worstPlayer = $statistics->rankingWorstPlayer($users);

    return response()->json($worstPlayer, 200);
  }
}
