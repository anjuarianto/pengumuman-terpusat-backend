<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserGroupHasUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserGroupHasUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserGroup::get()->each(function ($userGroup) {
            switch ($userGroup->id) {
                case UserGroup::DOSEN_ID:
                    User::where("email", "LIKE", "%" . User::DOSEN_DOMAIN . "%")->get()->each(function ($user) use ($userGroup) {
                        UserGroupHasUser::create([
                            'user_group_id' => $userGroup->id,
                            'user_id' => $user->id
                        ]);
                    });
                    break;
                case UserGroup::TENDIK_ID:
                    User::where("email", "LIKE", "%" . User::TENDIK_DOMAIN . "%")->get()->each(function ($user) use ($userGroup) {
                        UserGroupHasUser::create([
                            'user_group_id' => $userGroup->id,
                            'user_id' => $user->id
                        ]);
                    });
                    break;
                case UserGroup::MAHASISWA_ID:
                    User::where("email", "LIKE", "%" . User::MAHASISWA_DOMAIN . "%")->get()->each(function ($user) use ($userGroup) {
                        UserGroupHasUser::create([
                            'user_group_id' => $userGroup->id,
                            'user_id' => $user->id
                        ]);
                    });
                    break;
            }
        });

    }
}
