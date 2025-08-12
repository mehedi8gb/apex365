<?php

namespace App\Providers;

use App\Exceptions\Handler;
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
                    'name' => 'Diamond',
                    'threshold' => 50,
                    'coins' => 1000.0
                ],
                (object) [
                    'name' => 'Gold',
                    'threshold' => 23,
                    'coins' => 50.0
                ],
                (object) ['name' => 'Silver', 'threshold' => 10, 'coins' => 30.0],
                (object) ['name' => 'Bronze', 'threshold' => 5, 'coins' => 5.0],
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
