<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create 20 groups
        $groups = \App\Models\Group::factory(150)->create();

        // Create 1500 members and attach them to random groups
        \App\Models\Member::factory(1500)->create()->each(function ($member) use ($groups) {
            // Attach to 1-3 random groups
            $member->groups()->attach(
                $groups->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
