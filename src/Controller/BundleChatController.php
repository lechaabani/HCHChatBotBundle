<?php

namespace HCH\ChatBotBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use HCH\ChatBotBundle\Service\ChatBotService;

class BundleChatController extends AbstractController
{
    #[Route('/hch_chatbot/chat', name: 'hch_chatbot_chat', methods: ['POST'])]
    public function chat(Request $request, ChatBotService $chatBot): JsonResponse
    {
        $message = $request->request->get('message');
        $conversationId = $request->request->get('conversation_id');
        
        try {
            $response = $chatBot->sendMessage($message, $conversationId);
            return $this->json([
                'response' => $response,
                'conversation_id' => $conversationId
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
} 