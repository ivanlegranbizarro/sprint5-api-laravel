<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::table('games', function (Blueprint $table) {
      $table->integer('dice1')->default(0);
      $table->integer('dice2')->default(0);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('games', function (Blueprint $table) {
      $table->dropColumn(['dice1', 'dice2']);
    });
  }
};
