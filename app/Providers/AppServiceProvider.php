<?php

namespace App\Providers;

use App\Exceptions\Handler;
use App\Models\AdminRankSetting;
use App\Services\Admin\AdminRankService;
use App\Services\Admin\CommissionService;
use App\Services\Admin\ProfileRankService;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ExceptionHandler::class, Handler::class);
//        $this->app->singleton(ProfileRankService::class, function ($app) {
//            $service = $app->make(AdminRankService::class);
//            return new ProfileRankService($service->allAsObjects());
//        });


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(CommissionService $service): void
    {
//        $commissions = $service->getAll();
//
//        // Dynamically override config
//        config(['commissions' => $commissions]);
    }
}
