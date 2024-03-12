<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    protected $count;
    public function __construct($count = 10)
    {
        $this->count = $count;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        User::create([
            'name' => 'Dosen',
            'email' => 'dosen@if.itera.ac.id',
            'password' => Hash::make('password')
        ]);

        User::create([
            'name' => 'Mahasiswa',
            'email' => 'annike.120140041@student.itera.ac.id',
            'password' => Hash::make('password')
        ]);

        foreach (range(1, $this->count) as $index) {
            User::create([
                'name' => $faker->name,
                'email' => preg_replace('/@example\..*/', '@if.itera.ac.id', $faker->unique()->safeEmail),
                'password' => Hash::make('password')
            ]);
        }

        foreach (range(1, $this->count) as $index) {
            User::create([
                'name' => $faker->name,
                'email' => preg_replace('/@example\..*/', '@student.itera.ac.id', $faker->unique()->safeEmail),
                'password' => Hash::make('password')
            ]);
        }
    }
}
