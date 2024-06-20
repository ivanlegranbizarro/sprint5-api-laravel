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
  public function get_all_users_is_unauthorized_without_auth(): void
  {
    $this->getJson('/api/players')->assertUnauthorized();
  }

  #[Test]
  public function get_all_users_is_forbidden_without_admin(): void
  {
    $this->actingAs($this->user, 'api')->getJson('/api/players')->assertForbidden();
  }

  #[Test]
  public function get_all_players_with_auth_and_admin(): void
  {
    $this->actingAs($this->admin, 'api')->getJson('/api/players')->assertOk();
  }
}
