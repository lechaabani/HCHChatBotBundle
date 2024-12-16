<?php

namespace HCH\ChatBotBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use HCH\ChatBotBundle\Service\ChatBotService;
use HCH\ChatBotBundle\Service\Analytics\AnalyticsService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[Route('/api/v1/chat', name: 'api_chat_')]
class ChatApiController extends AbstractController
{
    public function __construct(
        private ChatBotService $chatService,
        private AnalyticsService $analyticsService
    ) {}

    #[Route('/send', methods: ['POST'])]
    #[OA\Post(
        path: '/api/v1/chat/send',
        description: 'Envoie un message au HCHChatBotBundle',
        tags: ['Chat'],
        parameters: [
            new OA\Parameter(
                name: 'message',
                in: 'body',
                required: true,
                description: 'Message à envoyer',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'context',
                in: 'body',
                required: false,
                description: 'Contexte additionnel',
                schema: new OA\Schema(type: 'object')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Message traité avec succès'
            )
        ]
    )]
    public function send(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['message'])) {
            throw new BadRequestHttpException('Le message est requis');
        }

        $response = $this->chatService->processMessage(
            $data['message'],
            $data['context'] ?? []
        );

        return $this->json([
            'success' => true,
            'response' => $response
        ]);
    }

    #[Route('/history', methods: ['GET'])]
    public function history(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        
        $history = $this->chatService->getHistory($page, $limit);

        return $this->json([
            'success' => true,
            'data' => $history
        ]);
    }

    #[Route('/stats', methods: ['GET'])]
    public function stats(): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->json([
            'success' => true,
            'data' => $this->analyticsService->getStatistics()
        ]);
    }
} 