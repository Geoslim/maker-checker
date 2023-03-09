<?php

namespace Database\Seeders;

use App\Enums\Role;
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
        RoleService::assignRoles($user, [Role::CHECKER]);

        $user3 = [
            'first_name' => 'Daemon',
            'last_name' => 'Targaryen',
            'email' => 'daemon@gmail.com',
            'password' => 'secret'
        ];

        $user = UserService::createUser($user3);
        RoleService::assignRoles($user, [Role::MAKER]);

        $user4 = [
            'first_name' => 'Rhaegar',
            'last_name' => 'Targaryen',
            'email' => 'rhaegar@gmail.com',
            'password' => 'secret'
        ];

        $user = UserService::createUser($user4);
        RoleService::assignRoles($user, [Role::MAKER]);

        $user5 = [
            'first_name' => 'Jamie',
            'last_name' => 'Lannister',
            'email' => 'jamie@gmail.com',
            'password' => 'secret'
        ];

        $user = UserService::createUser($user5);
        RoleService::assignRoles($user, [Role::USER]);
    }
}
