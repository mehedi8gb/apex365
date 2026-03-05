<?php
// File: app/Jobs/ProcessPurchaseReferralChain.php

namespace App\Jobs;

use App\Jobs\Base\BaseReferralJob;
use App\Models\User;
use Throwable;

class ProcessPurchaseReferralChain extends BaseReferralJob
{
    private User $user;

    public function __construct(User $user)
    {
        parent::__construct();
        $this->user = $user;
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->updateReferralChain($this->user);
        $this->distributeReferralPoints('purchase');
        $this->updateReferralLeaderboard();
    }
}
