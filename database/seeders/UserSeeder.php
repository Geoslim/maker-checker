<?php

namespace Database\Seeders;

use App\Enum\Role;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = [
            'first_name' => 'Viserys',
            'last_name' => 'Targaryen',
            'email' => 'viserys@gmail.com',
            'password' => 'secret'
        ];

        $user = UserService::createUser($user1);
        RoleService::assignRoles($user, [Role::CHECKER, Role::MAKER]);

        $user2 = [
            'first_name' => 'Aegon',
            'last_name' => 'Targaryen',
            'email' => 'aegon@gmail.com',
            'password' => 'secret'
        ];

        $user = UserService::createUser($user2);
        RoleService::assignRoles($user, [Role::CHECKER, Role::MAKER]);
    }
}
