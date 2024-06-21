<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GameControllerTest extends TestCase
{
  use RefreshDatabase, WithFaker;

  protected $user;
  protected $admin;
  public function setUp(): void
  {
    parent::setUp();

    Artisan::call('passport:client --name=<client-name> --no-interaction --personal');

    $this->user = User::factory()->create([
      'email' => 'ivan@ivan.com',
      'password' => bcrypt('password'),
    ]);
    $this->admin = User::factory()->create([
      'email' => 'admin@admin.com',
      'password' => bcrypt('password'),
      'role' => 'admin',
    ]);
  }

  #[Test]
  public function games_need_authenticated_user_to_be_created(): void
  {
    $this->postJson('/api/players/games', [])->assertUnauthorized();

    $this->actingAs($this->user, 'api')->postJson('/api/players/games', [])->assertCreated();
  }

  #[Test]
  public function admin_can_display_all_games_grouped_by_players(): void
  {
    $this->getJson('/api/players/games/admin')->assertUnauthorized();

    $response = $this->actingAs($this->admin, 'api')->getJson('/api/players/games/admin');

    $response->assertOk()
      ->assertJsonStructure([
        '*' => [
          'player_id',
          'nickname',
          'games' => [
            '*' => [
              'result',
              'won',
            ],
          ],
        ],
      ]);
  }
}
