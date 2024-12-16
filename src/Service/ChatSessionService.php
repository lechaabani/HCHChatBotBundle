<?php

namespace HCH\ChatBotBundle\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ChatSessionService
{
    private AdapterInterface $cache;
    private RequestStack $requestStack;

    public function __construct(
        RequestStack $requestStack,
        AdapterInterface $cache
    ) {
        $this->requestStack = $requestStack;
        $this->cache = $cache;
    }

    private function getSession(): ?SessionInterface
    {
        try {
            $session = $this->requestStack->getSession();
            return $session;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getConversationHistory(): array
    {
        $session = $this->getSession();
        if (!$session) {
            return [];
        }
        return $session->get('chatbot_history', []);
    }

    public function addToHistory(string $message, string $response, array $context = []): void
    {
        $session = $this->getSession();
        if (!$session || headers_sent()) {
            $this->saveToCache($message, $response, $context);
            return;
        }

        $history = $session->get('chatbot_history', []);
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

        $session->set('chatbot_history', array_slice($history, -50));
    }

    private function saveToCache(string $message, string $response, array $context = []): void
    {
        $key = 'chatbot_history_' . md5($message . time());
        $data = [
            'message' => $message,
            'response' => $response,
            'context' => $context,
            'timestamp' => time()
        ];
        
        $item = $this->cache->getItem($key);
        $item->set($data);
        $item->expiresAfter(3600 * 24);
        $this->cache->save($item);
    }

    public function clearHistory(): void
    {
        $session = $this->getSession();
        if ($session) {
            $session->remove('chatbot_history');
        }
    }

    public function getSessionId(): ?string
    {
        $session = $this->getSession();
        return $session ? $session->getId() : null;
    }

    public function setContext(string $key, mixed $value): void
    {
        $session = $this->getSession();
        if (!$session) {
            return;
        }

        $context = $session->get('chatbot_context', []);
        $context[$key] = $value;
        $session->set('chatbot_context', $context);
    }

    public function getContext(string $key, mixed $default = null): mixed
    {
        $session = $this->getSession();
        if (!$session) {
            return $default;
        }

        $context = $session->get('chatbot_context', []);
        return $context[$key] ?? $default;
    }

    public function persistSession(): void
    {
        $sessionId = $this->getSessionId();
        if (!$sessionId) {
            return;
        }

        $data = [
            'history' => $this->getConversationHistory(),
            'context' => $this->getSession()?->get('chatbot_context', []) ?? []
        ];

        $item = $this->cache->getItem('chatbot_session_' . $sessionId);
        $item->set($data);
        $item->expiresAfter(3600 * 24);
        $this->cache->save($item);
    }

    public function restoreSession(string $sessionId): bool
    {
        $session = $this->getSession();
        if (!$session) {
            return false;
        }

        $item = $this->cache->getItem('chatbot_session_' . $sessionId);
        if (!$item->isHit()) {
            return false;
        }

        $data = $item->get();
        $session->set('chatbot_history', $data['history'] ?? []);
        $session->set('chatbot_context', $data['context'] ?? []);
        return true;
    }
} 