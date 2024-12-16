<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use HCH\ChatBotBundle\Service\ChatBotService;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'app_chat', methods: ['POST'])]
    public function chat(Request $request, ChatBotService $chatBot): JsonResponse
    {
        $message = $request->request->get('message');
        $conversationId = $request->request->get('conversation_id');

        try {
            $response = $chatBot->sendMessage($message, $conversationId);
            return $this->json(['response' => $response]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/chat', name: 'app_chat_page')]
    public function index(): Response
    {
        return $this->render('chat/index.html.twig');
    }
} 