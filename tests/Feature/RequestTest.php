<?php

namespace Tests\Feature;

use App\Enums\RequestType;
use App\Enums\Status;
use App\Jobs\NotifyAdministrators;
use App\Models\Request;
use App\Models\Request as RequestModel;
use App\Models\User;
use App\Notifications\RequestNotification;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use Tests\UserTestCase;

class RequestTest extends UserTestCase
{
    public function testAdminCanMakeACreateRequest()
    {
        Bus::fake();
        Notification::fake();

        $data = [
            'first_name' => 'George',
            'last_name' => 'Samson',
            'email' => 'george@gmail.com',
        ];

        $response = $this->actingAs($this->makerUser, 'api')
            ->postJson('api/users/create', $data)
            ->assertSuccessful();

        $this->assertDatabaseHas("requests", [
            'type' => RequestType::CREATE,
            'maker_id' => $this->makerUser->id,
            'data->email' => $data['email'],
            'status' => Status::PENDING->value,
        ]);

        Bus::assertDispatched(NotifyAdministrators::class);
    }

    /**
     * Attempt to create a request with a role that isn't MAKER or CHECKER
     */
    public function testUnauthorizedAttemptToMakeRequest()
    {
        Bus::fake();

        $data = [
            'first_name' => 'George',
            'last_name' => 'Samson',
            'email' => 'george@gmail.com',
        ];

        $this->actingAs($this->user, 'api')
            ->postJson('api/users/create', $data)
            ->assertUnauthorized();

        $this->assertDatabaseMissing("requests", [
            'data->email' => $data['email']
        ]);

        Bus::assertNotDispatched(NotifyAdministrators::class);
    }

    public function testAdminCanMakeAnUpdateRequest()
    {
        Bus::fake();

        $user = User::factory()->create();

        $newData = [
            'first_name' => 'George',
            'last_name' => 'Samson',
            'email' => 'george@gmail.com',
        ];

        $this->actingAs($this->makerUser, 'api')
            ->postJson("api/users/{$user->id}/update", $newData)
            ->assertSuccessful();

        $this->assertDatabaseHas("requests", [
            'type' => RequestType::UPDATE->value,
            'maker_id' => $this->makerUser->id,
            'status' => Status::PENDING->value,
            'data->email' => $newData['email']
        ]);

        Bus::assertDispatched(NotifyAdministrators::class);
    }

    public function testAdminCanMakeADeleteRequest()
    {
        Bus::fake();
        $user = User::factory()->create();

        $this->actingAs($this->makerUser, 'api')
            ->postJson("api/users/{$user->id}/delete")
            ->assertSuccessful();

        $this->assertDatabaseHas("requests", [
            'type' => RequestType::DELETE->value,
            'status' => Status::PENDING->value,
        ]);

        Bus::assertDispatched(NotifyAdministrators::class);
    }

    public function testSameRequestAlreadyExists()
    {
        $user = User::factory()->create();

        $data = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email
        ];

        $this->createRequest($this->makerUser->id, RequestType::UPDATE->value, $user->id, $data);

        $data['email'] = 'georgia@gmail.com';

        $response = $this->actingAs($this->makerUser, 'api')
            ->postJson("api/users/{$user->id}/update", $data);

        $this->assertEquals('Sorry, there is a pending request for this user.', $response['message']);
        $this->assertFalse($response['success']);
    }

    protected function createRequest($authUserId, $requestType, $userId = null, $data = null)
    {
         return RequestModel::create([
            'user_id' => $userId ?? null,
            'type' => $requestType,
            'data' => $data ?? null,
            'maker_id' => $authUserId
        ]);
    }

    public function testFetchingPendingRequests()
    {
        $user = User::factory()->create();

        $this->createRequest($this->makerUser->id, RequestType::DELETE->value, $user->id);

        $response = $this->actingAs($this->makerUser, 'api')
            ->getJson('api/requests')
            ->assertSuccessful()
            ->assertJsonStructure(['data']);

        $this->assertTrue($response['success']);
    }

    public function testApprovingACreateRequest()
    {
        $data = [
            'first_name' => 'George',
            'last_name' => 'Alexander',
            'email' => 'alex@gmail.com',
        ];

        $request = $this->createRequest($this->makerUser->id, RequestType::CREATE->value, null, $data);

        $response = $this->actingAs($this->checkerUser, 'api')
            ->postJson("api/requests/{$request->id}/approve");

        $this->assertEquals('Request successfully approved.', $response['message']);
        $this->assertTrue($response['success']);
        $request->refresh();
        $this->assertEquals(Status::APPROVED->value, $request['status']);
        $this->assertDatabaseHas("users", [
            'first_name' => $data['first_name'],
            'email' => $data['email']
        ]);
    }

    public function testApprovingAnUpdateRequest()
    {
        $user = User::factory()->create();

        $newData = [
            'first_name' => 'Varys',
            'last_name' => $user['last_name'],
            'email' => 'varys@gmail.com'
        ];

        $request = $this->createRequest($this->makerUser->id, RequestType::UPDATE->value, $user->id, $newData);

        $response = $this->actingAs($this->checkerUser, 'api')
            ->postJson("api/requests/{$request->id}/approve");

        $this->assertEquals('Request successfully approved.', $response['message']);
        $this->assertTrue($response['success']);
        $request->refresh();
        $this->assertEquals(Status::APPROVED->value, $request['status']);
        $this->assertDatabaseHas("users", [
            'first_name' => $newData['first_name'],
            'last_name' => $user['last_name'],
            'email' => $newData['email']
        ]);
    }

    public function testApprovingADeleteRequest()
    {
        $user = User::factory()->create();

        $request = $this->createRequest($this->makerUser->id, RequestType::DELETE->value, $user->id);

        $response = $this->actingAs($this->checkerUser, 'api')
            ->postJson("api/requests/{$request->id}/approve")
            ->assertSuccessful();

        $this->assertEquals('Request successfully approved.', $response['message']);
        $this->assertTrue($response['success']);
        $request->refresh();
        $this->assertEquals(Status::APPROVED->value, $request['status']);
        $this->assertSoftDeleted("users", [
            'last_name' => $user['last_name'],
            'email' => $user['email']
        ]);
    }

    /**
     * Only an administrator with CHECKER role can approve or decline a request.
     */
    public function testAdminWithoutCheckerRoleCannotApproveRequest()
    {
        $user = User::factory()->create();

        $request = $this->createRequest($this->makerUser->id, RequestType::DELETE->value, $user->id);

        $response = $this->actingAs($this->makerUser, 'api')
            ->postJson("api/requests/{$request->id}/approve")
            ->assertUnauthorized();

        $this->assertEquals('You are not authorized to carry out this action', $response['message']);
        $this->assertFalse($response['success']);
    }

    public function testDecliningARequest()
    {
        $user = User::factory()->create();

        $request = $this->createRequest($this->makerUser->id, RequestType::DELETE->value, $user->id);

        $this->actingAs($this->checkerUser, 'api')
            ->postJson("api/requests/{$request->id}/decline")
            ->assertSuccessful();

        $this->assertSoftDeleted("requests", [
            'type' => RequestType::DELETE->value,
            'maker_id' => $this->makerUser->id
        ]);

        $this->assertDatabaseHas("users", [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email']
        ]);
    }

    /**
     * Testing Notification separately because it's inside a job.
     */
    public function testNotificationIsSent()
    {
        Notification::fake();

        $user = User::factory()->create();

        $request = $this->createRequest($this->makerUser->id, RequestType::DELETE->value, $user->id);

        $job = new NotifyAdministrators($request, $this->makerUser->id);
        $job->handle();

        Notification::assertSentTo($this->checkerUser, RequestNotification::class);
    }
}
