<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use App\Enums\UserRole;
use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected UserRepositoryInterface $userRepository;

    /**
     * Inject the specific decoupled user repository contract.
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle user registration logic.
     */
    public function register(array $data): array
    {
        // Object creation offloaded safely to the abstraction layer
        $user = $this->userRepository->create([
            'name'         => $data['name'],
            'job_title'    => $data['job_title'] ?? null,
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
            'role'         => UserRole::from($data['role']),
            'department'   => $data['department'] ?? null,
            'bio'          => $data['bio'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        /**
         * @var \App\Models\User $user
         */
event(new UserRegistered($user));
        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    /**
     * Handle user authentication credentials check.
     */
    public function login(array $data): ?array
    {
        // Querying data strictly through the interface signature mapping
        $user = $this->userRepository->findByEmail($data['email']);
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return null; 
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    /**
     * Revoke current user access tokens.
     */
    public function logout($user): bool
    {
        if ($user) {
            $user->currentAccessToken()->delete();
            return true;
        }
        return false;
    }
}