<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RoleService
{
    /**
     * @param User|Model $user
     * @param array $roles
     * @return void
     */
    public static function assignRoles(User|Model $user, array $roles): void
    {
        $roleId = Role::whereIn('name', $roles)->pluck('id');
        $user->roles()->sync($roleId);
    }
}
