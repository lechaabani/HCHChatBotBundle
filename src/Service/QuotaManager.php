<?php

namespace HCH\ChatBotBundle\Service;

use Symfony\Component\Cache\Adapter\AdapterInterface;

class QuotaManager
{
    public function __construct(
        private AdapterInterface $cache,
        private string $resetPeriod
    ) {}

    public function incrementUsage(string $provider): void
    {
        $key = $this->getProviderKey($provider);
        $item = $this->cache->getItem($key);
        $count = $item->get() ?? 0;
        $item->set($count + 1);
        $this->cache->save($item);
    }

    public function getUsage(string $provider): int
    {
        $key = $this->getProviderKey($provider);
        $item = $this->cache->getItem($key);
        return $item->get() ?? 0;
    }

    private function getProviderKey(string $provider): string
    {
        return sprintf('llm_quota_%s_%s', $provider, date('Y-m-d'));
    }
} 