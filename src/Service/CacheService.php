<?php

namespace HCH\ChatBotBundle\Service;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

class CacheService
{
    public function __construct(
        private CacheItemPoolInterface $cache,
        private array $config
    ) {}

    public function getCachedResponse(string $message, array $context = []): ?string
    {
        if (!$this->config['cache']['enabled']) {
            return null;
        }

        $key = $this->generateCacheKey($message, $context);
        $item = $this->cache->getItem($key);

        return $item->isHit() ? $item->get() : null;
    }

    public function cacheResponse(string $message, string $response, array $context = []): void
    {
        if (!$this->config['cache']['enabled']) {
            return;
        }

        $key = $this->generateCacheKey($message, $context);
        $item = $this->cache->getItem($key);
        
        $item->set($response)
            ->expiresAfter($this->config['cache']['ttl'] ?? 3600);

        if ($this->cache instanceof TagAwareAdapterInterface) {
            $item->tag(['HCHChatBotBundle', 'response']);
        }

        $this->cache->save($item);
    }

    private function generateCacheKey(string $message, array $context): string
    {
        return md5(serialize([
            'message' => $message,
            'context' => $context
        ]));
    }

    public function clearCache(): void
    {
        if ($this->cache instanceof TagAwareAdapterInterface) {
            $this->cache->invalidateTags(['HCHChatBotBundle']);
        } else {
            $this->cache->clear();
        }
    }
} 