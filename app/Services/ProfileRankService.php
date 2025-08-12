<?php
namespace App\Services;

class ProfileRankService
{
    private array $ranks;

    /**
     * Accepts an array of rank definitions (objects or arrays)
     * Each rank must have: name, threshold, coins
     *
     * @param array<int, object{ name: string, threshold: int, coins: float }> $ranks
     */
    public function __construct(array $ranks)
    {
        // Sort ranks descending by threshold to pick highest matching rank first
        usort($ranks, fn($a, $b) => $b->threshold <=> $a->threshold);
        $this->ranks = $ranks;
    }

    public function getRankName(int $totalNodes): string
    {
        foreach ($this->ranks as $rank) {
            if ($totalNodes >= $rank->threshold) {
                return $rank->name;
            }
        }
        return 'Newbie'; // fallback default
    }

    public function getEarnedCoins(int $totalNodes): float
    {
        foreach ($this->ranks as $rank) {
            if ($totalNodes >= $rank->threshold) {
                return $rank->coins;
            }
        }
        return 0.0; // fallback default
    }
}
