<?php

namespace App\Jobs;

use App\Helpers\ReferralHelper;
use App\Models\ReferralCode;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProcessReferralChain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;
    public ReferralCode $referrerAndCode;

    public function __construct(User $user, ReferralCode $referrerAndCode)
    {
        $this->user = $user;
        $this->referrerAndCode = $referrerAndCode;

        $this->onQueue('referral-chain'); // named queue for clarity (optional)
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $helper = new ReferralHelper();

        $helper->createReferralChain($this->user, $this->referrerAndCode);
        $helper->distributeReferralPoints();
        $helper->updateReferralLeaderboard();
        $helper->generateReferralCode();
    }
}

