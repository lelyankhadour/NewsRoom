<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\ProfileService;
use App\Traits\ApiResponse;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\JsonResponse;


class ProfileController extends Controller
{
    use ApiResponse;

    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
      }

    /**
     * Route API Endpoint for mutating core profile configurations.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            // Data arrives here already validated and safe
            $user = $this->profileService->updateBasicInfo($request->user(), $request->validated());

            return $this->successResponse(new UserResource($user), "Profile identity updated successfully.");
        } catch (\Throwable $exception) {
            return $this->errorResponse("Failed to update user profile parameters.", 500);
        }
    }

  
}