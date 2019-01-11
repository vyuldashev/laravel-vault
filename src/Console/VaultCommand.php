<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelVault\Console;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Vyuldashev\LaravelVault\Stores\APCu;
use Vyuldashev\LaravelVault\Vault;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\ConfirmableTrait;
use Vyuldashev\LaravelVault\Contracts\Store;
use Vyuldashev\LaravelVault\Stores\Memcached;

class VaultCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'vault
                            {store=memcached}
                            {host=127.0.0.1}
                            {port=11211}
                            {--force : Force the operation to run when in production.}';
    protected $description = 'Create a secure cached configuration file';

    public function handle(Vault $vault, Filesystem $filesystem): void
    {
        $cachedConfigPath = $this->laravel->getCachedConfigPath();

        if (
            $filesystem->exists($cachedConfigPath) &&
            !$this->confirmToProceed('This command will override your cached configuration. Are you sure?')
        ) {
            return;
        }

        $vault->secure(
            $this->createStore(),
            $cachedConfigPath,
            [
                $this->laravel->basePath() . '/.env',
            ]
        );

        $this->info('Configuration secured successfully!');
    }

    /**
     * Get store instance.
     * Currently, only Memcached is supported.
     *
     * @return Store
     */
    private function createStore(): Store
    {
        switch ($this->argument('store')) {
            case 'memcached':
                $store = new Memcached();
                break;
            case 'apcu':
                $store = new APCu();
                break;
            default:
                throw new InvalidArgumentException('Unknown store.');
        }

        return $store->create($this->arguments());
    }
}
