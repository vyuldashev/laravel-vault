<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelVault;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\VaultCommand::class,
            ]);
        }
    }
}
