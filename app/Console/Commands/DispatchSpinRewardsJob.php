<?php

namespace App\Console\Commands;

use App\Jobs\ProcessSpinRewardsJob;
use Illuminate\Console\Command;

class DispatchSpinRewardsJob extends Command
{
    protected $signature = 'spin:dispatch-rewards';

    protected $description = 'Dispatch the spin rewards job to be processed';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        // Dispatch the job to the queue
        ProcessSpinRewardsJob::dispatch()->onQueue('spin-rewards');

        $this->info('Spin Rewards Job dispatched!');
    }
}

