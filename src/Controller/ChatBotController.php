<?php

namespace HCH\ChatBotBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use HCH\ChatBotBundle\Service\ChatBotService;

class ChatBotController extends AbstractController
{
    public function __construct(
        private ChatBotService $chatBotService
    ) {}

    public function send(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $message = $data['message'] ?? '';
        $context = $data['context'] ?? [];

        try {
            $response = $this->chatBotService->processMessage($message, $context);
            return $this->json(['success' => true, 'response' => $response]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function chat(): Response
    {
        return $this->render('@HCHChatBot/chat/index.html.twig');
    }
} 