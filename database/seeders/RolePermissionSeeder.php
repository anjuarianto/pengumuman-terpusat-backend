<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = Permission::all();

        $permission_mahasiswa = [
            'view-pengumuman', 'view-pengumuman-reply', 'create-pengumuman-reply', 'edit-pengumuman-reply', 'delete-pengumuman-reply'
        ];

        foreach($permissions as $permission) {
            Role::where('name', 'dosen')->first()->givePermissionTo($permission);
        }

        foreach($permission_mahasiswa as $permission) {
            Role::where('name', 'mahasiswa')->first()->givePermissionTo($permission);
        }
    }
}
