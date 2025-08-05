<?php

namespace App\Actions;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class CustomerAction
{
    public static function handleUpdate(array $validated, User $user): ?User
    {
//        $shouldInvalidateToken = self::hasSensitiveChanges($validated, $user);

        self::syncRoleIfNeeded($validated, $user);
        self::updateUser($user, $validated);

        return $user;
    }

    private static function hasSensitiveChanges(array $validated, User $user): bool
    {
        return
            (isset($validated['password'])) ||
            (isset($validated['email']) && $validated['email'] !== $user->email) ||
            (isset($validated['phone']) && $validated['phone'] !== $user->phone);
    }

    private static function syncRoleIfNeeded(array $validated, User $user): void
    {
        if (isset($validated['role'])) {
            $user->syncRoles($validated['role']);
        }
    }

    private static function updateUser(User $user, array $validated): void
    {
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);
    }

//    private static function invalidateUserTokens(User $user): void
//    {
//        try {
//            if ($token = JWTAuth::getToken()) {
//                JWTAuth::invalidate($token);
//            }
//        } catch (\Throwable $e) {
//            report($e);
//        }
//
//        $user->update(['token_version' => now()->timestamp]);
//    }
}

