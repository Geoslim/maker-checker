<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $response = $this->authService->register($request->validated());
            return $this->successResponse($response);
        } catch (Exception $e) {
            Log::error($e);
            return $this->error($e->getMessage());
        }

    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $response = $this->authService->login($request->validated());
            return $this->successResponse($response);
        } catch (Exception $e) {
            Log::error($e);
            return $this->error($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request);
            return $this->success('Successfully logged out.');
        } catch (Exception $e) {
            Log::error($e);
            return $this->error('An error occurred');
        }
    }
}
