<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'result' => $this->result,
      'won' => $this->won ? 'Yes' : 'No',
      'dice1' => $this->dice1,
      'dice2' => $this->dice2,
      'played_at' => $this->created_at->format('Y-m-d H:i:s'),
    ];
  }
}
