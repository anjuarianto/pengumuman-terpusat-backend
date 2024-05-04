<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Room::create([
            'id' => 1,
            'name' => 'General',
            'description' => 'Ini adalah category general'
        ]);

        Room::factory(10)->create();

        $rooms = Room::all();
    }
}
