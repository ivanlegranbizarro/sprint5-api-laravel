<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'user_id' => User::all()->random()->id,
      'dice1' => rand(1, 6),
      'dice2' => rand(1, 6),
      'result' => rand(1, 6) + \rand(1, 6),
      'won' => rand(0, 1),
    ];
  }
}
