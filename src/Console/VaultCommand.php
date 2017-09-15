<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelVault\Console;

use Illuminate\Console\Command;
use Vyuldashev\LaravelVault\Vault;
use Illuminate\Filesystem\Filesystem;
use Vyuldashev\LaravelVault\Contracts\Store;
use Vyuldashev\LaravelVault\Stores\Memcached;

class VaultCommand extends Command
{
    protected $signature = 'vault
                            {host=127.0.0.1}
                            {port=11211}';
    protected $description = 'Create a secure cached configuration file';

    public function handle(Vault $vault, Filesystem $filesystem): void
    {
        $store = $this->createStore();

        $cachedConfigPath = $this->laravel->getCachedConfigPath();

        if ($filesystem->exists($cachedConfigPath) && !$this->confirm('This command will override your cached configuration. Are you sure?')) {
            exit(0);
        }

        $vault->secure(
            $store,
            $cachedConfigPath,
            [
                $this->laravel->basePath().'/.env',
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
        return (new Memcached)->create($this->arguments());
    }
}
