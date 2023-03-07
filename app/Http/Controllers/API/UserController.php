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

    public function create(CreateUserRequest $request)
    {
        try {
            $response = $this->requestService->create($request->validated(), $request->user()->id);
            // we need to send notification
            return $this->successResponse(RequestResource::make($response));
        } catch (Exception $e) {
            Log::error($e);
            return $this->error($e->getMessage());
        }
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $data = $request->validated();
//            $userId = $data['user_id'];
//            unset($data['user_id']);
            $response = $this->requestService->update($data, $user->id, $request->user()->id);
            // we need to send notification
            return $this->successResponse(RequestResource::make($response));
        } catch (Exception $e) {
            Log::error($e);
            return $this->error($e->getMessage());
        }
    }

    public function delete(Request $request, User $user): JsonResponse
    {
        try {
            $response = $this->requestService->delete($user->id, $request->user()->id);
            // we need to send notification
            return $this->successResponse(RequestResource::make($response));
        } catch (Exception $e) {
            Log::error($e);
            return $this->error($e->getMessage());
        }
    }
}
