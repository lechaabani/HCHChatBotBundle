<?php

namespace HCH\ChatBotBundle\Cache;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ResponseCache
{
    private $cache;
    private $ttl;

    public function __construct(CacheInterface $cache, int $ttl = 3600)
    {
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    public function get(string $key, callable $callback)
    {
        return $this->cache->get($key, function (ItemInterface $item) use ($callback) {
            $item->expiresAfter($this->ttl);
            return $callback();
        });
    }
} 