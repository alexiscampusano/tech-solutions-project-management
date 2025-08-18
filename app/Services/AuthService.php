<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

final class AuthService
{
    /**
     * Register a new user
     */
    public function register(array $userData): array
    {
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
        ]);

        $token = JWTAuth::fromUser($user);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ];
    }

    /**
     * Authenticate user and generate token
     */
    public function login(array $credentials): array
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            throw new \Exception('Credenciales incorrectas', 401);
        }

        $user = Auth::user();

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ];
    }

    /**
     * User logout
     */
    public function logout(): void
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $e) {
            throw new \Exception('Error al cerrar sesión', 500);
        }
    }

    /**
     * Get authenticated user information
     */
    public function getAuthenticatedUser(): array
    {
        $user = Auth::user();

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'proyectos_count' => $user->proyectos->count()
            ]
        ];
    }

    /**
     * Refresh JWT token
     */
    public function refreshToken(): array
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            return [
                'token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ];
        } catch (JWTException $e) {
            throw new \Exception('Error al refrescar token', 500);
        }
    }

    /**
     * Check if email is already in use
     */
    public function emailExists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    /**
     * Check if a token is valid
     */
    public function validateToken(string $token): bool
    {
        try {
            JWTAuth::setToken($token);
            return JWTAuth::check();
        } catch (JWTException $e) {
            return false;
        }
    }
}
