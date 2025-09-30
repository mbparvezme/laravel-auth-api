<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Profile::create([
            'user_id' => 1,
            'profile_picture' => 'profiles/default.png',
            'mobile' => '+8801712345678',
            'address' => 'House 123, Road 4, Dhaka, Bangladesh',
            'dob' => '1990-01-01',
            'gender' => 'male',
            'bio' => 'This is a sample bio for user 1.',
        ]);
    }
}
