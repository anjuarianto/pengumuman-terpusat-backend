<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        Module Pengumuman, PengumumanReply, Room, UserGroup
        $permissions = [
            'view-pengumuman', 'create-pengumuman', 'edit-pengumuman',  'delete-pengumuman',
            'view-pengumuman-reply', 'create-pengumuman-reply', 'edit-pengumuman-reply', 'delete-pengumuman-reply',
            'view-room', 'create-room', 'edit-room', 'delete-room',
            'view-user-group', 'create-user-group', 'edit-user-group', 'delete-user-group',
        ];

        foreach($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'api'
            ]);
        }
    }
}
