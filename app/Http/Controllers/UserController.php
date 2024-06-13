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
   * Display a listing of the resource.
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
   * Store a newly created resource in storage.
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
   * Login the specified user
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
   * Display the specified resource.
   */
  public function show(User $user): JsonResponse
  {
    Gate::authorize('view', $user);
    return response()->json(UserShowResource::make($user), 200);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UpdateNicknameRequest $request, User $user): JsonResponse
  {
    $data = $request->validated();
    $user->update($data);

    return response()->json(['message' => 'Nickname updated successfully'], 200);
  }
}
