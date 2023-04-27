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
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile', 11)->after('email')->unique()->nullable();
            $table->timestamp('mobile_verified_at')->after('email_verified_at')->nullable();
            $table->string('country', 3)->after('remember_token');
            $table->string('country_code', 3)->after('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('mobile');
            $table->dropColumn('mobile_verified_at');
            $table->dropColumn('country');
            $table->dropColumn('country_code');
        });
    }
};
