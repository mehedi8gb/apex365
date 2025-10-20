<?php
// File: app/Jobs/Base/BaseReferralJob.php

namespace App\Jobs\Base;

use App\Helpers\ReferralHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class BaseReferralJob extends ReferralHelper implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct()
    {
        $this->onQueue('referral-chain');
    }

    /**
     * Must be implemented by child jobs.
     */
    abstract public function handle(): void;
}
