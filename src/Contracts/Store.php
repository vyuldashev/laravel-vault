<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelVault\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface Store extends Arrayable
{
    /**
     * Create a new store instance.
     *
     * @param array $options
     *
     * @return Store
     */
    public function create(array $options): Store;

    /**
     * Put item in store.
     *
     * @param string $key
     * @param mixed $item
     *
     * @return bool
     */
    public function put(string $key, $item): bool;

    /**
     * Retrieve an item from the store.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key);
}
