<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelVault;

use RuntimeException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Config\Repository;
use Vyuldashev\LaravelVault\Contracts\Store;

class Vault
{
    private const PREFIX = 'vault';

    private $config;
    private $filesystem;

    public function __construct(Repository $config, Filesystem $filesystem)
    {
        $this->config = $config;
        $this->filesystem = $filesystem;
    }

    public function secure(Store $store, string $file, array $cleanup = []): void
    {
        if (!$store->put($key = $this->generateKey(), require $file)) {
            throw new RuntimeException('Could not put data to store.');
        }

        $contents = '<?php $store = new '.get_class($store).'; ';
        $contents .= '$store->create('.var_export($store->toArray(), true).'); ';
        $contents .= 'return $store->get(\''.$key.'\');';

        $this->filesystem->put($file, $contents);

        collect($cleanup)->each(function ($item) {
            $this->filesystem->delete($item);
        });
    }

    /**
     * Generate random key, which will be used to store contents.
     *
     * @return string
     */
    private function generateKey(): string
    {
        $appName = $this->config->get('app.name');
        $appName = md5($appName);

        return implode('_', [self::PREFIX, $appName]);
    }
}
