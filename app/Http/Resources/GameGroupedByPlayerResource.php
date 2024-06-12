<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\GameResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GameGroupedByPlayerResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'player_id' => $this->user_id,
      'nickname' => $this->nickname,
      'games' => GameResource::collection($this->games),
    ];
  }
}
