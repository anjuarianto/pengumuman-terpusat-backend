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
        UserGroup::create([
            'id' => UserGroup::DOSEN_ID,
            'name' => 'Dosen'
        ]);

        UserGroup::create([
            'id' => UserGroup::TENDIK_ID,
            'name' => 'Tendik'
        ]);

        UserGroup::create([
            'id' => UserGroup::MAHASISWA_ID,
            'name' => 'Mahasiswa'
        ]);
    }
}
