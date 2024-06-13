<?php

namespace App\Services;

class StatisticsService
{
  public function calculateSuccessPercentage(array $games): float
  {
    // Implementar la lógica para calcular el porcentaje de éxito
    $totalGames = count($games);
    if ($totalGames === 0) {
      return 0.0;
    }
    $wonGames = count(array_filter($games, function ($game) {
      return $game['is_won']; // Suponiendo que 'is_won' indica si el juego fue ganado
    }));
    return ($wonGames / $totalGames) * 100;
  }

  public function calculateAverageSuccessPercentage(array $players): float
  {
    // Implementar la lógica para calcular el porcentaje de éxito promedio
    $totalPlayers = count($players);
    if ($totalPlayers === 0) {
      return 0.0;
    }
    $totalPercentage = array_sum(array_map(function ($player) {
      return $player['success_percentage']; // Suponiendo que 'success_percentage' es el porcentaje de éxito del jugador
    }, $players));
    return $totalPercentage / $totalPlayers;
  }

  public function getBestPlayer(array $players)
  {
    // Implementar la lógica para obtener el jugador con mejor porcentaje de éxito
    return collect($players)->sortByDesc('success_percentage')->first();
  }

  public function getWorstPlayer(array $players)
  {
    // Implementar la lógica para obtener el jugador con peor porcentaje de éxito
    return collect($players)->sortBy('success_percentage')->first();
  }
}
