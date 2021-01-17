<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ACLSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()['cache']->forget('spatie.permission.cache');

        $roles = [
            'superadmin',
            'admin',
            'manager',
            'user'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $user_super_admin = User::where('email', '=', 'superadmin@admin.com')->first();

        if (!$user_super_admin) {

            $user_super_admin = new User;

            $user_super_admin->email = 'superadmin@admin.com';
            $user_super_admin->password ='superadmin';

            $user_super_admin->save();
        }

        if ($user_super_admin && !$user_super_admin->hasRole('superadmin')) {

            $user_super_admin->assignRole('superadmin');
        }

        $user_admin = User::where('email', '=', 'admin@admin.com')->first();

        if (!$user_admin) {

            $user_admin = new User;

            $user_admin->email = 'admin@admin.com';
            $user_admin->password ='admin';

            $user_admin->save();
        }

        if ($user_admin && !$user_admin->hasRole('admin')) {

            $user_admin->assignRole('admin');
        }
    }
}
