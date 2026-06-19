<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Predictable traders
        User::factory()
            ->hasAssets()
            ->has(Asset::factory()->eth())
            ->create([
                'name' => 'Anthony Nnanna',
                'email' => 'anthonynnanna@virgosoft.com',
            ]);

        //Unpredictable traders
        User::factory()
            ->count(3)
            ->hasAssets()
            ->has(Asset::factory()->eth())
            ->create();
    }
}
