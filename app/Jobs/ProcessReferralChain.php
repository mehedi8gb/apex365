<?php
// File: app/Jobs/ProcessReferralChain.php

namespace App\Jobs;

use App\Jobs\Base\BaseReferralJob;
use App\Models\ReferralCode;
use App\Models\User;
use Throwable;

class ProcessReferralChain extends BaseReferralJob
{
    private User $user;
    private ReferralCode $referrerAndCode;

    public function __construct(User $user, ReferralCode $referrerAndCode)
    {
        parent::__construct();
        $this->user = $user;
        $this->referrerAndCode = $referrerAndCode;
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->createReferralChain($this->user, $this->referrerAndCode);
        $this->distributeReferralPoints();
        $this->updateReferralLeaderboard();
        $this->generateReferralCode();
    }
}
