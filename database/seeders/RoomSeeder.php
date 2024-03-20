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
            'description' => 'Ini adalah room general'
        ]);

        Room::factory(10)->create();

        $rooms = Room::all();

        $rooms->each(function ($room) {
            foreach (range(1, 10) as $index) {
                $is_single_user = rand(0, 1);
                $room->members()->create([
                    'user_id' => $is_single_user ? User::all()->random()->id : UserGroup::all()->random()->id,
                    'is_single_user' => $is_single_user
                ]);
            }
        });
    }
}
