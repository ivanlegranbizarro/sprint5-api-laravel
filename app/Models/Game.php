<?php

namespace App\Models;

use Illuminate\Support\Collection;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Game extends Model
{
  use HasFactory;


  protected $fillable = [
    'user_id',
    'won',
    'result',
    'dice1',
    'dice2'
  ];


  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public static function getGamesGroupedByPlayer(): Collection
  {
    return self::with('user')
      ->get()
      ->groupBy('user_id')
      ->map(function ($games, $user_id) {
        $user = $games->first()->user;
        $playerGames = [];
        foreach ($games as $game) {
          $playerGames[] = [
            'result' => $game->result,
            'won' => $game->won,
          ];
        }
        return (object) [
          'player_id' => $user_id,
          'nickname' => $user->nickname,
          'games' => $playerGames,
        ];
      });
  }
}
