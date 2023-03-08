<?php

namespace Tests;

use App\Enums\Role;
use App\Models\User;
use App\Services\RoleService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserTestCase extends TestCase
{
    public $checkerUser;
    public $makerUser;
    public $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->checkerUser = $this->checkerUser();
        $this->makerUser = $this->makerUser();
        $this->user = $this->user();
    }

    protected function checkerUser()
    {
        $user = User::factory()->create();

        RoleService::assignRoles($user, [Role::CHECKER]);

        return $user;
    }

    protected function makerUser()
    {
        $user = User::factory()->create();

        RoleService::assignRoles($user, [Role::MAKER]);

        return $user;
    }

    protected function user()
    {
        $user = User::factory()->create();

        RoleService::assignRoles($user, [Role::USER]);

        return $user;
    }
}
