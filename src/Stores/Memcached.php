<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelVault\Stores;

use Memcached as Connection;
use Vyuldashev\LaravelVault\Contracts\Store;

class Memcached implements Store
{
    private const DEFAULT_HOST = '127.0.0.1';
    private const DEFAULT_PORT = 11211;

    /** @var Connection|null */
    private $connection;

    private $host;
    private $port;

    /**
     * Create a new store instance.
     *
     * @param array $options
     *
     * @return Store
     */
    public function create(array $options): Store
    {
        $host = $options['host'] ?? self::DEFAULT_HOST;
        $port = (int) ($options['port'] ?? self::DEFAULT_PORT);

        $connection = new Connection;
        $connection->addServer($host, $port);

        $this->connection = $connection;
        $this->host = $host;
        $this->port = $port;

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
        return $this->connection->set($key, $item);
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
        return $this->connection->get($key);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return ['host' => $this->host, 'port' => $this->port];
    }
}
