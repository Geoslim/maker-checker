<?php

namespace App\Services;

use App\Enums\Role;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    /**
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        $user = UserService::createUser($data);
        RoleService::assignRoles($user, [Role::defaultRole()]);
        $user->load('roles');
        return $this->getResponse($user);
    }

    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function login(array $data): array
    {
        $user = User::query()->whereEmail($data['email'])
            ->with('roles')
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new Exception('Invalid credentials', Response::HTTP_BAD_REQUEST);
        }

        return $this->getResponse($user);
    }

    /**
     * @param User $user
     * @return array
     */
    protected function getResponse(User $user): array
    {
        return [
            'user' => UserResource::make($user),
            'token' => $user->createToken($user->email)->accessToken
        ];
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function logout(Request $request): bool
    {
        abort_unless(
            $request->user()->tokecns()->delete(),
            Response::HTTP_BAD_REQUEST
        );

        return true;
    }
}
