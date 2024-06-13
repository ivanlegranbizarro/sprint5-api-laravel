<?php

namespace App\Services;

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
    return ($wonGames / $totalGames) * 100;
  }
}
