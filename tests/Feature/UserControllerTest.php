<?php

namespace Tests\Feature;

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
}
