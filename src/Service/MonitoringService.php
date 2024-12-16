<?php

namespace HCH\ChatBotBundle\Service;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class MonitoringService
{
    private Connection $connection;
    private LoggerInterface $logger;
    private string $environment;
    private array $config;

    public function __construct(
        Connection $connection,
        LoggerInterface $logger,
        string $environment = 'prod',
        array $config = []
    ) {
        $this->connection = $connection;
        $this->logger = $logger;
        $this->environment = $environment;
        $this->config = $config;
    }

    public function getProvidersStatus(): array
    {
        $providers = [];
        foreach ($this->config['providers'] as $name => $config) {
            $providers[$name] = [
                'name' => $name,
                'status' => $this->getProviderStatus($name),
                'quota_used' => $this->getQuotaUsed($name),
                'quota_total' => $config['limits']['daily_requests'] ?? null,
                'latency' => $this->getAverageLatency($name)
            ];
        }
        return $providers;
    }

    public function getDetailedProvidersInfo(): array
    {
        // Implémentation détaillée...
        return [];
    }

    public function getRecentLogs(int $limit = 100): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT * FROM chatbot_logs ORDER BY created_at DESC LIMIT :limit',
            ['limit' => $limit]
        );
    }

    private function getProviderStatus(string $provider): string
    {
        // Implémentation du statut...
        return 'active';
    }

    private function getQuotaUsed(string $provider): int
    {
        // Implémentation du quota...
        return 0;
    }

    private function getAverageLatency(string $provider): float
    {
        // Implémentation de la latence...
        return 0.0;
    }
} 