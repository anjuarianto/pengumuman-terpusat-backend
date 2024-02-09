<?php

namespace Database\Seeders;

use App\Models\Pengunguman;
use Illuminate\Database\Seeder;

class PengungumanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pengunguman::factory(100)->create();
    }
}
