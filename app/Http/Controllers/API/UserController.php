<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request\CreateUserRequest;
use App\Http\Requests\Request\UpdateUserRequest;
use App\Http\Resources\RequestResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\RequestService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct(protected RequestService $requestService)
    {
    }

    public function index(): JsonResponse
    {
        return $this->successResponse(
            UserResource::collection(User::orderBy('id', 'desc')->get()),
        );
    }

    /**
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function create(CreateUserRequest $request): JsonResponse
    {
        try {
            $response = $this->requestService->create($request->validated(), $request->user()->id);
            return $this->successResponse(RequestResource::make($response));
        } catch (Exception $e) {
            Log::error($e);
            return $this->error($e->getMessage());
        }
    }

    /**
     * @param UpdateUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $data = $request->validated();
            $response = $this->requestService->update($data, $user->id, $request->user()->id);
            return $this->successResponse(RequestResource::make($response));
        } catch (Exception $e) {
            Log::error($e);
            return $this->error($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function delete(Request $request, User $user): JsonResponse
    {
        try {
            $response = $this->requestService->delete($user->id, $request->user()->id);
            return $this->successResponse(RequestResource::make($response));
        } catch (Exception $e) {
            Log::error($e);
            return $this->error($e->getMessage());
        }
    }
}
