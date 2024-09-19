<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Enums\UserRolesEnum;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // create roles and assign created permissions
        $role = Role::create(['name' => UserRolesEnum::SUPERADMIN]);
        $role->givePermissionTo([Permission::all()]);


        $role = Role::create(['name' => UserRolesEnum::ADMIN]);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => UserRolesEnum::JUDGE]);
        $role->givePermissionTo(Permission::all());


        $user = new User;
        $user->name = 'Super Admin';
        $user->email = 'superadmin@admin.com';
        $user->password = Hash::make('password');
        $user->original_password = 'password';
        $user->email_verified_at = now();
        $user->role = UserRolesEnum::SUPERADMIN;
        $user->save();
        
    }
}