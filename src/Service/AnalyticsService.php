<?php

namespace HCH\ChatBotBundle\Service;

use Doctrine\DBAL\Connection;

class AnalyticsService
{
    public function __construct(
        private Connection $connection,
        private MonitoringService $monitoringService
    ) {}

    public function getDashboardStats(): array
    {
        return [
            'daily_requests' => $this->getDailyRequestCount(),
            'total_cost' => $this->calculateTotalCost(),
            'average_response_time' => $this->getAverageResponseTime(),
            'success_rate' => $this->calculateSuccessRate()
        ];
    }

    public function trackConversation(string $message, string $response, array $context): void
    {
        // Enregistrement des statistiques de conversation
        $this->connection->insert('chatbot_conversations', [
            'message' => $message,
            'response' => $response,
            'provider' => $context['provider'] ?? 'unknown',
            'tokens' => $context['tokens'] ?? 0,
            'duration' => $context['duration'] ?? 0,
            'created_at' => new \DateTime()
        ]);
    }

    private function getDailyRequestCount(): int
    {
        return (int) $this->connection->fetchOne(
            'SELECT COUNT(*) FROM chatbot_conversations WHERE DATE(created_at) = CURRENT_DATE'
        );
    }

    // ... autres méthodes d'analytics
} 