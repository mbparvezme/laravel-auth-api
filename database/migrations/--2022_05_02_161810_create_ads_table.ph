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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->integer('provider_id');
            $table->tinyText('provider_type');
            $table->string('title');
            $table->integer('category');
            $table->string('images');
            $table->string('price');
            $table->json('available_location');
            $table->boolean('delivery_by_seller'); // included, excluded, negotiable
            $table->json('data');
            $table->timestamps();
            $table->tinyInteger('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ads');
    }
};
