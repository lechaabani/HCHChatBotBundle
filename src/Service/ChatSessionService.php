<?php

namespace HCH\ChatBotBundle\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class ChatSessionService
{
    private SessionInterface $session;
    private AdapterInterface $cache;

    public function __construct(
        SessionInterface $session,
        AdapterInterface $cache
    ) {
        $this->session = $session;
        $this->cache = $cache;
    }

    public function getConversationHistory(): array
    {
        return $this->session->get('chatbot_history', []);
    }

    public function addToHistory(string $message, string $response, array $context = []): void
    {
        $history = $this->getConversationHistory();
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

        $this->session->set('chatbot_history', array_slice($history, -50)); // Garde les 50 derniers messages
    }

    public function clearHistory(): void
    {
        $this->session->remove('chatbot_history');
    }

    public function getSessionId(): string
    {
        return $this->session->getId();
    }

    public function setContext(string $key, mixed $value): void
    {
        $context = $this->session->get('chatbot_context', []);
        $context[$key] = $value;
        $this->session->set('chatbot_context', $context);
    }

    public function getContext(string $key, mixed $default = null): mixed
    {
        $context = $this->session->get('chatbot_context', []);
        return $context[$key] ?? $default;
    }

    public function persistSession(): void
    {
        $sessionId = $this->session->getId();
        $data = [
            'history' => $this->getConversationHistory(),
            'context' => $this->session->get('chatbot_context', [])
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
        $this->session->set('chatbot_history', $data['history'] ?? []);
        $this->session->set('chatbot_context', $data['context'] ?? []);
        return true;
    }
} 