<?php

namespace App\Providers;

use App\Exceptions\Handler;
use App\Services\CommissionService;
use App\Services\ProfileRankService;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ExceptionHandler::class, Handler::class);
        $this->app->singleton(ProfileRankService::class, function () {
            return new ProfileRankService([
                (object) [
                    'name' => 'Pro Platinum',
                    'threshold' => 10,
                    'coins' => 30000.0,
                ],
                (object) [
                    'name' => 'Platinum',
                    'threshold' => 8,
                    'coins' => 20000.0,
                ],
                (object) [
                    'name' => 'Diamond',
                    'threshold' => 6,
                    'coins' => 10000.0,
                ],
                (object) [
                    'name' => 'Gold',
                    'threshold' => 4,
                    'coins' => 5000.0,
                ],
                (object) [
                    'name' => 'Silver',
                    'threshold' => 2,
                    'coins' => 2500.0,
                ],
                (object) [
                    'name' => 'Bronze',
                    'threshold' => 1,
                    'coins' => 1000.0,
                ],
            ]);
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(CommissionService $service): void
    {
        $commissions = $service->getAll();

        // Dynamically override config
        config(['commissions' => $commissions]);
    }
}
