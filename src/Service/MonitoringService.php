<?php

namespace HCH\ChatBotBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;

class MonitoringService
{
    public function __construct(
        private LoggerInterface $logger,
        private string $environment
    ) {}

    public function trackRequest(string $provider, string $model, int $tokens, float $duration): void
    {
        $this->logger->info('LLM Request', [
            'provider' => $provider,
            'model' => $model,
            'tokens' => $tokens,
            'duration' => $duration,
            'env' => $this->environment
        ]);
    }

    public function trackError(string $provider, string $errorMessage): void
    {
        $this->logger->error('LLM Error', [
            'provider' => $provider,
            'error' => $errorMessage,
            'env' => $this->environment
        ]);
    }
} 