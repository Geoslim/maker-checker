<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     * @param array $data
     * @return User
     */
    public static function createUser(array $data): User
    {
        return User::create($data);
    }

    public static function updateUser(User $user, array $data): User
    {
        $user->update($data);
        return $user->refresh();
    }

    public static function deleteUser(User $user)
    {
        return $user->delete();
    }
}
