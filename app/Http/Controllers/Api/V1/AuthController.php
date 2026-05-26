<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Api\V1\ProfileResource;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ApiResponse;

    protected AuthService $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * Endpoint for user self-registration.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->service->register($request->validated());

            $data = [
                'user'  => new ProfileResource($result['user']),
                'token' => $result['token'],
            ];

            return $this->successResponse($data, "Registration completed successfully", 201);

        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Endpoint for user authentication.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->service->login($request->validated());

            // Catch the null value returned from the service safely without crashing
            if (is_null($result)) {
                return $this->errorResponse("Invalid credentials provided.", 401);
            }

            $data = [
                'user'  => new ProfileResource($result['user']),
                'token' => $result['token'],
            ];

            return $this->successResponse($data, "Logged in successfully", 200);

        } catch (\Throwable $e) {
            return $this->errorResponse("Something went wrong during authentication processing.", 500);
        }
    }

    /**
     * Endpoint for killing active login session.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->service->logout($request->user());
            return $this->successResponse(null, "Logged out successfully", 200);

        } catch (\Throwable $e) {
            return $this->errorResponse("Something went wrong during sign out operation.", 500);
        }
    }
}