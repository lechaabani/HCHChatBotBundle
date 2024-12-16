<?php

namespace HCH\ChatBotBundle\Service;

use Symfony\Component\Cache\Adapter\AdapterInterface;

class UsageTracker
{
    public function __construct(
        private AdapterInterface $cache
    ) {}

    public function incrementDailyMessageCount(string $userId): void
    {
        $key = $this->getDailyKey($userId);
        $item = $this->cache->getItem($key);
        
        $count = $item->get() ?? 0;
        $item->set($count + 1);
        
        // Expire à minuit
        $item->expiresAt(new \DateTime('tomorrow midnight'));
        
        $this->cache->save($item);
    }

    public function getDailyMessageCount(string $userId): int
    {
        $key = $this->getDailyKey($userId);
        $item = $this->cache->getItem($key);
        
        return $item->get() ?? 0;
    }

    private function getDailyKey(string $userId): string
    {
        return sprintf('chatbot_daily_messages_%s_%s', $userId, date('Y-m-d'));
    }
} 