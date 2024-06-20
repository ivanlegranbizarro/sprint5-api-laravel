<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class UserControllerTest extends TestCase
{
  use RefreshDatabase, WithFaker;

  public function setUp(): void
  {
    parent::setUp();

    Artisan::call('passport:client --name=<client-name> --no-interaction --personal');
  }

  #[Test]
  public function new_player_can_be_registered(): void
  {
    $this->postJson('/api/players', [
      'email' => 'ivan@ivan.com',
      'password' => 'password',
    ])->assertCreated();
  }
}
