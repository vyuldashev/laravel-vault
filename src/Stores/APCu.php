<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelVault\Stores;

use APCIterator;
use Vyuldashev\LaravelVault\Contracts\Store;

class APCu implements Store
{
    /**
     * Create a new store instance.
     *
     * @param array $options
     *
     * @return Store
     */
    public function create(array $options): Store
    {
        return $this;
    }

    /**
     * Put item in store.
     *
     * @param string $key
     * @param mixed $item
     *
     * @return bool
     */
    public function put(string $key, $item): bool
    {
        return apcu_add($key, $item);
    }

    /**
     * Retrieve an item from the store.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return apcu_fetch($key);
    }

    /**
     * Clear all items saved in the store.
     *
     * @param string $prefix
     * @param string|null $except
     */
    public function clear(string $prefix, string $except = null): void
    {
        $iterator = new APCIterator('user');
        foreach ($iterator as $item) {
            if ($item['key'] !== $except && starts_with($item['key'], $prefix)) {
                apcu_delete($item['key']);
            }
        }
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }
}
