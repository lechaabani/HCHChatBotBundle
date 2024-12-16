<?php

namespace HCH\ChatBotBundle\Service;

use Doctrine\DBAL\Connection;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class QuotaManager
{
    private Connection $connection;
    private AdapterInterface $cache;
    private string $resetPeriod;

    public function __construct(
        Connection $connection,
        AdapterInterface $cache,
        string $resetPeriod = 'daily'
    ) {
        $this->connection = $connection;
        $this->cache = $cache;
        $this->resetPeriod = $resetPeriod;
    }

    public function incrementUsage(string $provider): void
    {
        $key = $this->getProviderKey($provider);
        $item = $this->cache->getItem($key);
        $count = $item->get() ?? 0;
        $item->set($count + 1);
        $this->cache->save($item);

        // Log usage in database
        $this->connection->insert('chatbot_quota_usage', [
            'provider' => $provider,
            'count' => 1,
            'date' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getUsage(string $provider): int
    {
        $key = $this->getProviderKey($provider);
        $item = $this->cache->getItem($key);
        return $item->get() ?? 0;
    }

    public function resetQuota(string $provider): void
    {
        $key = $this->getProviderKey($provider);
        $this->cache->deleteItem($key);
    }

    private function getProviderKey(string $provider): string
    {
        return sprintf('llm_quota_%s_%s', $provider, date('Y-m-d'));
    }
} 