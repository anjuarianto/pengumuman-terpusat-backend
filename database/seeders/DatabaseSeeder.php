<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $userSeeder = new UserSeeder();
        $userSeeder->run();
        
        $pengungumanSeeder = new PengungumanSeeder();
        $pengungumanSeeder->run();
    }
}
