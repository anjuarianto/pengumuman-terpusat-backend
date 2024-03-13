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
            new UserSeeder(30),
            new RoleSeeder(),
            new UserGroupSeeder(),
            new RoomSeeder(),
            new PermissionSeeder(),
            new RolePermissionSeeder(),
            new PengumumanSeeder(),
        ];

        foreach ($seeders as $seeder) {
            $seeder->run();
        }
    }
}
