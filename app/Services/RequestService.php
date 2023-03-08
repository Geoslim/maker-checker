<?php

namespace App\Services;

use App\Enums\RequestType;
use App\Enums\Status;
use App\Jobs\NotifyAdministrators;
use App\Models\Request as RequestModel;
use Exception;

class RequestService
{
    /**
     * @param array $data
     * @param int $authUserId
     * @return mixed
     */
    public function create(array $data, int $authUserId): mixed
    {
        $request = RequestModel::create([
            'type' => RequestType::CREATE->value,
            'data' => $data,
            'maker_id' => $authUserId
        ]);

        $this->notifyAdministrators($request->load(['user', 'maker']), $authUserId);

        return $request;
    }

    /**
     * @param array $data
     * @param int $userId
     * @param int $authUserId
     * @return mixed
     * @throws Exception
     */
    public function update(array $data, int $userId, int $authUserId): mixed
    {
        $this->abortIfRequestExists($userId);

        $request = RequestModel::create([
            'user_id' => $userId,
            'type' => RequestType::UPDATE->value,
            'data' => $data,
            'maker_id' => $authUserId
        ]);

        $this->notifyAdministrators($request->load(['user', 'maker']), $authUserId);

        return $request;
    }

    /**
     * @param int $userId
     * @param int $authUserId
     * @return mixed
     * @throws Exception
     */
    public function delete(int $userId, int $authUserId): mixed
    {
        $this->abortIfRequestExists($userId);

        $request = RequestModel::create([
            'user_id' => $userId,
            'type' => RequestType::DELETE->value,
            'data' => null,
            'maker_id' => $authUserId
        ]);

        $this->notifyAdministrators($request->load(['user', 'maker']), $authUserId);

        return $request;
    }

    /**
     * @param RequestModel $request
     * @param int $authUserId
     * @return bool
     */
    public function approve(RequestModel $request, int $authUserId): bool
    {
        match ($request->type) {
            RequestType::CREATE->value => UserService::createUser($request->data),
            RequestType::UPDATE->value => UserService::updateUser($request->user, $request->data),
            RequestType::DELETE->value => UserService::deleteUser($request->user),
            default => throw new \InvalidArgumentException('Invalid request type specified.')
        };

        $request->update([
            'checker_id' => $authUserId,
            'status' => Status::APPROVED,
            'approved_at' => now()
        ]);

        return true;
    }

    /**
     * @param RequestModel $request
     * @return bool|null
     */
    public function decline(RequestModel $request): ?bool
    {
        return $request->delete();
    }


    /**
     * @param int $userId
     * @return void
     * @throws Exception
     */
    protected function abortIfRequestExists(int $userId): void
    {
        if (
            RequestModel::whereUserId($userId)
                ->whereStatus(Status::PENDING)
                ->exists()
        ) {
            throw new Exception('Sorry, there is a pending request for this user.');
        }
    }

    /**
     * @param RequestModel $request
     * @param int $authUserId
     * @return void
     */
    protected function notifyAdministrators(RequestModel $request, int $authUserId): void
    {
        NotifyAdministrators::dispatch($request, $authUserId);
    }
}
