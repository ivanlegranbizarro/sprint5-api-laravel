<?php

namespace Tests\Feature;

use App\Models\Game;
use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
  use RefreshDatabase, WithFaker;

  protected $user;
  protected $admin;
  protected $player1;
  protected $player2;

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

    $this->player1 = User::factory()->create([
      'email' => 'player1@player1.com',
      'nickname' => 'player1',
      'password' => bcrypt('password'),
    ]);

    $this->player2 = User::factory()->create([
      'email' => 'player2@player2.com',
      'nickname' => 'player2',
      'password' => bcrypt('password'),
    ]);

    Game::create([
      'user_id' => $this->player1->id,
      'dice1' => 7,
      'dice2' => 7,
      'result' => 14,
      'won' => 1
    ]);

    Game::create([
      'user_id' => $this->player2->id,
      'dice1' => 1,
      'dice2' => 1,
      'result' => 2,
      'won' => 0
    ]);
  }

  #[Test]
  public function new_player_can_be_registered(): void
  {
    $this->postJson('/api/players', [
      'email' => 'test@test.com',
      'password' => 'password',
    ])->assertCreated();
  }

  #[Test]
  public function player_can_login(): void
  {
    $this->postJson('/api/players/login', [
      'email' => $this->user->email,
      'password' => 'password',
    ])->assertOk();
  }

  #[Test]
  public function get_all_players_only_if_auth_and_admin(): void
  {
    $this->getJson('/api/players')->assertUnauthorized();

    $this->actingAs($this->user, 'api')->getJson('/api/players')->assertForbidden();

    $this->actingAs($this->admin, 'api')->getJson('/api/players')->assertOk();
  }

  #[Test]
  public function user_nickname_must_be_anonymous_if_not_provided(): void
  {
    $this->assertEquals('Anonymous', $this->user->nickname);
  }

  #[Test]
  public function nickname_can_be_updated_only_if_auth_and_admin(): void
  {
    $this->putJson('/api/players/' . $this->user->id, [
      'nickname' => 'Iván',
    ])->assertUnauthorized();

    $this->actingAs($this->user, 'api')->putJson('/api/players/' . $this->user->id, [
      'nickname' => 'Iván'
    ])->assertForbidden();

    $this->actingAs($this->admin, 'api')->putJson('/api/players/' . $this->user->id, [
      'nickname' => 'Iván'
    ])->assertOk();
  }

  #[Test]
  public function admin_can_check_list_of_all_users_and_their_statistics(): void
  {
    Game::factory(10)->create([
      'user_id' => $this->user->id,
    ]);

    $this->getJson('/api/players')->assertUnauthorized();

    $response = $this->actingAs($this->admin, 'api')->getJson('/api/players');

    $response->assertOk();

    $responseData = $response->json();

    $foundUser = collect($responseData)->firstWhere('email', $this->user->email);

    $this->assertNotNull($foundUser, 'User with email ' . $this->user->email . ' not found in response.');

    $this->assertArrayHasKey('success_percentage', $foundUser);
    $this->assertIsNumeric($foundUser['success_percentage']);
  }

  #[Test]
  public function ranking_displays_correctly_only_if_auth_and_admin(): void
  {
    $this->actingAs($this->user, 'api')->getJson('/api/players/ranking')->assertForbidden();

    $this->actingAs($this->admin, 'api')->getJson('/api/players/ranking')->assertOk();

    $response = $this->actingAs($this->admin, 'api')->getJson('/api/players/ranking');

    $this->assertEquals($response->json()[0]['nickname'], 'player1');
    $this->assertEquals($response->json()[1]['nickname'], 'player2');
  }

  #[Test]
  public function best_player_displays_correctly_only_if_auth_and_admin(): void
  {
    $this->getJson('/api/players/ranking/winner')->assertUnauthorized();

    $this->actingAs($this->user, 'api')->getJson('/api/players/ranking/winner')->assertForbidden();

    $this->actingAs($this->admin, 'api')->getJson('/api/players/ranking/winner')->assertOk();

    $response = $this->actingAs($this->admin, 'api')->getJson('/api/players/ranking/winner');

    $this->assertEquals($response->json()['nickname'], 'player1');
  }

  #[Test]
  public function worst_player_displays_correctly_only_if_auth_and_admin(): void
  {
    $this->getJson('/api/players/ranking/loser')->assertUnauthorized();

    $this->actingAs($this->user, 'api')->getJson('/api/players/ranking/loser')->assertForbidden();

    $this->actingAs($this->admin, 'api')->getJson('/api/players/ranking/loser')->assertOk();

    $response = $this->actingAs($this->admin, 'api')->getJson('/api/players/ranking/loser');

    $this->assertEquals($response->json()['nickname'], 'player2');
  }
}
