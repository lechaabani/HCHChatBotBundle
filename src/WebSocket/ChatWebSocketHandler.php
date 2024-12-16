<?php

namespace HCH\ChatBotBundle\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use HCH\ChatBotBundle\Service\ChatBotService;
use HCH\ChatBotBundle\Service\Analytics\AnalyticsService;
use Symfony\Component\Security\Core\Security;
use Psr\Log\LoggerInterface;

class ChatWebSocketHandler implements MessageComponentInterface
{
    protected $clients;
    private $chatService;
    private $analyticsService;
    private $security;
    private $logger;
    private $clientsInfo = [];

    public function __construct(
        ChatBotService $chatService,
        AnalyticsService $analyticsService,
        Security $security,
        LoggerInterface $logger
    ) {
        $this->clients = new \SplObjectStorage;
        $this->chatService = $chatService;
        $this->analyticsService = $analyticsService;
        $this->security = $security;
        $this->logger = $logger;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->clientsInfo[$conn->resourceId] = [
            'userId' => null,
            'sessionId' => null,
            'connectedAt' => new \DateTime()
        ];

        $this->logger->info('Nouvelle connexion WebSocket', [
            'clientId' => $conn->resourceId
        ]);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $data = json_decode($msg, true);
            if (!isset($data['type'])) {
                throw new \InvalidArgumentException('Type de message manquant');
            }

            switch ($data['type']) {
                case 'auth':
                    $this->handleAuth($from, $data);
                    break;
                
                case 'message':
                    $this->handleChatMessage($from, $data);
                    break;
                
                case 'typing':
                    $this->broadcastTypingStatus($from, $data);
                    break;
                
                default:
                    throw new \InvalidArgumentException('Type de message invalide');
            }
        } catch (\Exception $e) {
            $this->logger->error('Erreur WebSocket', [
                'error' => $e->getMessage(),
                'clientId' => $from->resourceId
            ]);

            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Une erreur est survenue'
            ]));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        unset($this->clientsInfo[$conn->resourceId]);
        
        $this->logger->info('Connexion WebSocket fermée', [
            'clientId' => $conn->resourceId
        ]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->logger->error('Erreur WebSocket', [
            'error' => $e->getMessage(),
            'clientId' => $conn->resourceId
        ]);

        $conn->close();
    }

    private function handleAuth(ConnectionInterface $conn, array $data): void
    {
        if (!isset($data['token'])) {
            throw new \InvalidArgumentException('Token manquant');
        }

        // Ici, implémentez votre logique d'authentification
        $this->clientsInfo[$conn->resourceId]['userId'] = 'user_id';
        $this->clientsInfo[$conn->resourceId]['sessionId'] = 'session_id';

        $conn->send(json_encode([
            'type' => 'auth',
            'status' => 'success'
        ]));
    }

    private function handleChatMessage(ConnectionInterface $from, array $data): void
    {
        if (!isset($data['message'])) {
            throw new \InvalidArgumentException('Message manquant');
        }

        $response = $this->chatService->processMessage(
            $data['message'],
            $data['context'] ?? []
        );

        $this->analyticsService->trackConversation(
            $data['message'],
            $response,
            [
                'clientId' => $from->resourceId,
                'userId' => $this->clientsInfo[$from->resourceId]['userId'] ?? null,
                'type' => 'websocket'
            ]
        );

        $from->send(json_encode([
            'type' => 'message',
            'response' => $response
        ]));
    }

    private function broadcastTypingStatus(ConnectionInterface $from, array $data): void
    {
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send(json_encode([
                    'type' => 'typing',
                    'userId' => $this->clientsInfo[$from->resourceId]['userId'] ?? 'anonymous',
                    'status' => $data['status'] ?? false
                ]));
            }
        }
    }
} 