<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
  /**
   * Transform the resource collection into an array.
   *
   * @return array<int|string, mixed>
   */
  public function toArray(Request $request): array
  {
    return $this->collection->map(function ($user) {
      return [
        'id' => $user->id,
        'nickname' => $user->nickname,
        'email' => $user->email,
      ];
    })->all();
  }
}
