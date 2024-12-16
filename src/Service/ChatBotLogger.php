<?php

namespace HCH\ChatBotBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ChatBotLogger
{
    public function __construct(
        private LoggerInterface $logger,
        private RequestStack $requestStack,
        private array $config
    ) {}

    public function logConversation(string $message, string $response, array $context = []): void
    {
        $session = $this->requestStack->getSession();
        
        $this->logger->info('ChatBot Conversation', [
            'session_id' => $session->getId(),
            'user_id' => $session->get('user_id'),
            'message' => $message,
            'response' => $response,
            'context' => $context,
            'timestamp' => time()
        ]);
    }

    public function logError(\Throwable $error, array $context = []): void
    {
        $this->logger->error('ChatBot Error', [
            'error' => $error->getMessage(),
            'trace' => $error->getTraceAsString(),
            'context' => $context
        ]);
    }
} 