<?php

namespace Tests\Feature;

use Symfony\Component\HttpFoundation\Response;
use Tests\UserTestCase;

class AuthTest extends UserTestCase
{
    public function testUserCanRegister()
    {
        $response = $this->postJson('api/auth/register', [
            'first_name' => 'George',
            'last_name' => 'Samson',
            'email' => 'george@gmail.com',
            'password' => 'password'
        ])->assertSuccessful();

        $this->assertDatabaseHas("users", [
            'email' => $response['data']['user']['email'],
        ]);
    }

    public function testInvalidLoginCredentials()
    {
        $this->postJson('api/auth/login', [
            'email' => 'george@gmail.com',
            'password' => 'password'
        ])->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testCanLogin()
    {
        $response = $this->postJson('api/auth/login', [
            'email' => $this->makerUser->email,
            'password' => 'password'
        ])->assertSuccessful()->assertJsonStructure([
            'data' => ['user', 'token']
        ]);

        $this->assertEquals($response['data']['user']['first_name'], $this->makerUser->first_name);
    }

    public function testCanLogout()
    {
        $this->actingAs($this->makerUser, 'api')
            ->postJson('api/auth/logout')
            ->assertSuccessful();

        $this->assertEquals(0, $this->makerUser->tokens()->count());
    }
}
