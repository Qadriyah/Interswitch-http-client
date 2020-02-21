<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\QuickTellerAPIService;
use App\Repositories\QuickTellerAPIInterface;

class RepositoryServiceProvider extends ServiceProvider
{

    protected $repoBindings = [
        QuickTellerAPIInterface::class => QuickTellerAPIService::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->repoBindings as $interface => $service) {
            $this->app->bind($interface, $service);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
