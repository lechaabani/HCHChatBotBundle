<?php

namespace HCH\ChatBotBundle\Service;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class ChatBotLogger
{
    private Connection $connection;
    private LoggerInterface $logger;

    public function __construct(
        Connection $connection,
        LoggerInterface $logger
    ) {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function logMessage(string $message, string $response, array $context = []): void
    {
        // Log dans la base de données
        $this->connection->insert('chatbot_logs', [
            'level' => 'info',
            'message' => $message,
            'response' => $response,
            'context' => json_encode($context),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Log via le logger système
        $this->logger->info('ChatBot message', [
            'message' => $message,
            'response' => $response,
            'context' => $context
        ]);
    }

    public function logError(\Throwable $error, array $context = []): void
    {
        // Log dans la base de données
        $this->connection->insert('chatbot_logs', [
            'level' => 'error',
            'message' => $error->getMessage(),
            'context' => json_encode([
                'file' => $error->getFile(),
                'line' => $error->getLine(),
                'trace' => $error->getTraceAsString(),
                ...$context
            ]),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Log via le logger système
        $this->logger->error('ChatBot error', [
            'error' => $error->getMessage(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'context' => $context
        ]);
    }

    public function getRecentLogs(int $limit = 100): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT * FROM chatbot_logs ORDER BY created_at DESC LIMIT :limit',
            ['limit' => $limit]
        );
    }

    public function getErrorLogs(int $limit = 100): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT * FROM chatbot_logs WHERE level = :level ORDER BY created_at DESC LIMIT :limit',
            [
                'level' => 'error',
                'limit' => $limit
            ]
        );
    }

    public function clearOldLogs(int $days = 30): void
    {
        $this->connection->executeStatement(
            'DELETE FROM chatbot_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)',
            ['days' => $days]
        );
    }
} 