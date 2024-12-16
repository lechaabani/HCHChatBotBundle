<?php

namespace HCH\ChatBotBundle\Service;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Psr\Cache\InvalidArgumentException;

class CacheService
{
    private AdapterInterface $cache;
    private int $defaultTtl;

    public function __construct(
        AdapterInterface $cache,
        int $defaultTtl = 3600
    ) {
        $this->cache = $cache;
        $this->defaultTtl = $defaultTtl;
    }

    public function get(string $key, callable $callback = null, int $ttl = null): mixed
    {
        $item = $this->cache->getItem($key);

        if ($item->isHit()) {
            return $item->get();
        }

        if ($callback === null) {
            return null;
        }

        $value = $callback();
        $item->set($value);
        $item->expiresAfter($ttl ?? $this->defaultTtl);
        $this->cache->save($item);

        return $value;
    }

    public function set(string $key, mixed $value, int $ttl = null): void
    {
        $item = $this->cache->getItem($key);
        $item->set($value);
        $item->expiresAfter($ttl ?? $this->defaultTtl);
        $this->cache->save($item);
    }

    public function delete(string $key): void
    {
        try {
            $this->cache->deleteItem($key);
        } catch (InvalidArgumentException $e) {
            // Log or handle the error if needed
        }
    }

    public function clear(): void
    {
        $this->cache->clear();
    }

    public function has(string $key): bool
    {
        return $this->cache->hasItem($key);
    }
} 