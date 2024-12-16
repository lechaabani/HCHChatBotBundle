<?php

namespace HCH\ChatBotBundle\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ChatSessionService
{
    private SessionInterface $session;
    private AdapterInterface $cache;
    private RequestStack $requestStack;

    public function __construct(
        RequestStack $requestStack,
        AdapterInterface $cache
    ) {
        $this->requestStack = $requestStack;
        $this->cache = $cache;
    }

    private function getSession(): SessionInterface
    {
        if (!$this->requestStack->getSession()->isStarted()) {
            $this->requestStack->getSession()->start();
        }
        return $this->requestStack->getSession();
    }

    public function getConversationHistory(): array
    {
        return $this->getSession()->get('chatbot_history', []);
    }

    public function addToHistory(string $message, string $response, array $context = []): void
    {
        $session = $this->getSession();
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

        $session->set('chatbot_history', array_slice($history, -50)); // Garde les 50 derniers messages
    }

    public function clearHistory(): void
    {
        $this->getSession()->remove('chatbot_history');
    }

    public function getSessionId(): string
    {
        return $this->getSession()->getId();
    }

    public function setContext(string $key, mixed $value): void
    {
        $session = $this->getSession();
        $context = $session->get('chatbot_context', []);
        $context[$key] = $value;
        $session->set('chatbot_context', $context);
    }

    public function getContext(string $key, mixed $default = null): mixed
    {
        $context = $this->getSession()->get('chatbot_context', []);
        return $context[$key] ?? $default;
    }

    public function persistSession(): void
    {
        $sessionId = $this->getSessionId();
        $data = [
            'history' => $this->getConversationHistory(),
            'context' => $this->getSession()->get('chatbot_context', [])
        ];

        $item = $this->cache->getItem('chatbot_session_' . $sessionId);
        $item->set($data);
        $item->expiresAfter(3600 * 24); // 24 heures
        $this->cache->save($item);
    }

    public function restoreSession(string $sessionId): bool
    {
        $item = $this->cache->getItem('chatbot_session_' . $sessionId);
        if (!$item->isHit()) {
            return false;
        }

        $data = $item->get();
        $session = $this->getSession();
        $session->set('chatbot_history', $data['history'] ?? []);
        $session->set('chatbot_context', $data['context'] ?? []);
        return true;
    }
} 