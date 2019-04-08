<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelVault;

use Illuminate\Support\Str;
use RuntimeException;
use Illuminate\Filesystem\Filesystem;
use Vyuldashev\LaravelVault\Contracts\Store;

class Vault
{
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
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

        $store->clear($this->getPrefix(), $key);
    }

    /**
     * Generate random key, which will be used to store contents.
     *
     * @return string
     */
    private function generateKey(): string
    {
        return $this->getPrefix().'_'.str_random();
    }

    private function getPrefix(): string
    {
        return 'vault_'.Str::slug(config('app.name'), '_');
    }
}
