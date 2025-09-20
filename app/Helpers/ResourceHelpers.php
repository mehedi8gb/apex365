<?php

namespace App\Helpers;

use App\Models\User;

class ResourceHelpers
{
    /**
     * Format price consistently across resources.
     */
    public static function formatPrice($amount, $currency = 'USD'): string
    {
        return number_format($amount, 2) . ' ' . $currency;
    }

    /**
     * Mask sensitive fields like phone numbers.
     */
    public static function maskPhone(string $phone): string
    {
        return substr($phone, 0, 3) . '****' . substr($phone, -2);
    }

    /**
     * Generate a full image URL.
     */
    public static function imageUrl(?string $path): ?string
    {
        return $path ? url('storage/' . $path) : null;
    }

    /**
     * Recursively build referral chain up to $maxLevel
     *
     * @param User $user The user whose referral chain is to be built.
     * @param int|null $maxLevel The maximum depth of the referral chain.
     * @param int $currentLevel The current level in the recursion (default is 1).
     * @return array The referral chain as a nested array.
     */
    public static function buildReferralChain(User $user, int $currentLevel = 1, int $maxLevel = null): array
    {
        $maxLevel = $maxLevel
            ?? (config('commissions.signup')
                ? count(config('commissions.signup'))
                : 5);

        // Stop if max level reached, no referrer
        if ($currentLevel > $maxLevel || !$user->referredBy) return [];


        $referrer = $user->referredBy->referrer;

        if (!$referrer) {
            return [];
        }

        return [
            'level' => $currentLevel,
            'name' => $referrer->name,
            'phone' => $referrer->phone,
            'referred_by' => self::buildReferralChain($referrer, $maxLevel, $currentLevel + 1),
        ];
    }

    public static function paginationMeta($paginator): ?array
    {
        return $paginator ? [
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'pagination_key' => $paginator->getPageName(),
        ] : [
            'total' => 0,
            'per_page' => 0,
            'current_page' => 0,
            'last_page' => 0,
            'pagination_key' => null,
        ];
    }
}
