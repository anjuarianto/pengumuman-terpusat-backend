<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
    }
}
