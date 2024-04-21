<?php

namespace Database\Seeders;

use App\Models\UserGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserGroupSeeder extends Seeder
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
        $userGroup = ['Tendik', 'Dosen', 'Mahasiswa'];

        foreach ($userGroup as $group) {
            UserGroup::create([
                'name' => $group
            ]);
        }
    }
}
