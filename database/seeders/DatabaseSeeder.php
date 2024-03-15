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
        $seeders = [
            new PermissionSeeder(),
            new RoleSeeder(),
            new RolePermissionSeeder(),
            new UserGroupSeeder(),
            new UserSeeder(30),
            new RoomSeeder(),
            new PengumumanSeeder(),
        ];

        foreach ($seeders as $seeder) {
            $seeder->run();
        }
    }
}
