<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('token_id')->unique();
            $table->foreign('token_id')->references('id')->on('personal_access_tokens')->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('platform', 32)->nullable();
            $table->string('device', 16)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_sessions');
    }
};
