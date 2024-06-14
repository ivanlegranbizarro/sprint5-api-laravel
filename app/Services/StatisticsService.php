<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class StatisticsService
{
  public function calculateSuccessPercentage(array $games): float
  {
    $totalGames = count($games);
    if ($totalGames === 0) {
      return 0.0;
    }
    $wonGames = count(array_filter($games, function ($game) {
      return $game['won'];
    }));
    $successPercentage = ($wonGames / $totalGames) * 100;
    return round($successPercentage, 2);
  }

  public function rankingAllPlayers(Collection $users): Collection
  {
    foreach ($users as $user) {
      $user->success_percentage = $this->calculateSuccessPercentage($user->games->toArray());
    }
    return $users;
  }

  public function rankingBestPlayer(Collection $users): User
  {
    $users = $this->rankingAllPlayers($users);
    $bestPlayer = collect($users)->sortByDesc('success_percentage')->first();
    return $bestPlayer;
  }

  public function rankingWorstPlayer(Collection $users): User
  {
    $users = $this->rankingAllPlayers($users);
    $worstPlayer = collect($users)->sortBy('success_percentage')->first();
    return $worstPlayer;
  }
}
