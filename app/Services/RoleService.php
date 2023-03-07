<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;

class RoleService
{
    /**
     * @param User $user
     * @param array $roles
     * @return void
     */
    public static function assignRoles(User $user, array $roles): void
    {
        $roleId = Role::whereIn('name', $roles)->pluck('id');
        $user->roles()->sync($roleId);
    }
}
