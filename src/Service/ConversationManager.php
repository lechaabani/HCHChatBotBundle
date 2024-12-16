<?php

namespace HCH\ChatBotBundle\Service;

use Symfony\Component\Cache\Adapter\AdapterInterface;

class ConversationManager
{
    private AdapterInterface $cache;
    private int $historyLimit;

    public function __construct(
        AdapterInterface $cache,
        int $historyLimit = 50
    ) {
        $this->cache = $cache;
        $this->historyLimit = $historyLimit;
    }

    public function addToHistory(string $conversationId, string $message, string $response, array $context = []): void
    {
        $item = $this->cache->getItem('conversation_' . $conversationId);
        $history = $item->get() ?? [];
        
        $history[] = [
            'role' => 'user',
            'content' => $message,
            'timestamp' => time()
        ];
        $history[] = [
            'role' => 'assistant',
            'content' => $response,
            'context' => $context,
            'timestamp' => time()
        ];

        // Garde les N derniers messages
        $history = array_slice($history, -$this->historyLimit);
        
        $item->set($history);
        $item->expiresAfter(3600 * 24); // 24 heures
        $this->cache->save($item);
    }

    public function getHistory(string $conversationId): array
    {
        $item = $this->cache->getItem('conversation_' . $conversationId);
        return $item->get() ?? [];
    }

    public function clearHistory(string $conversationId): void
    {
        $this->cache->deleteItem('conversation_' . $conversationId);
    }
} 