<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('otps', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user');
      $table->string('action');
      $table->string('sent_to');
      $table->string('otp', 6);
      $table->timestamp('expired_at');
      $table->string('token')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('otps');
  }
};
