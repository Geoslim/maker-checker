<?php

namespace App\Services;

use App\Enums\RequestType;
use App\Enums\Status;
use App\Exceptions\HttpException;
use App\Jobs\NotifyAdministrators;
use App\Models\Request as RequestModel;
use Exception;
use InvalidArgumentException;

class RequestService
{
    /**
     * @param int $authUserId
     * @param string $requestType
     * @param int|null $userId
     * @param array|null $data
     * @return mixed
     * @throws Exception
     */
    public function createRequest(int $authUserId, string $requestType, ?int $userId = null, ?array $data = null): mixed
    {
        $this->abortIfRequestExists($userId);

        $request = RequestModel::create([
            'user_id' => $userId ?? null,
            'type' => $requestType,
            'data' => $data ?? null,
            'maker_id' => $authUserId
        ]);

        $this->notifyAdministrators($request->refresh()->load(['user', 'maker']), $authUserId);

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
            default => throw new InvalidArgumentException('Invalid request type specified.')
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
     * @param int|null $userId
     * @return void
     * @throws Exception
     */
    protected function abortIfRequestExists(?int $userId = null): void
    {
        if (
            !is_null($userId) &&
            RequestModel::whereUserId($userId)
                ->whereStatus(Status::PENDING)
                ->exists()
        ) {
            throw new HttpException('Sorry, there is a pending request for this user.');
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
