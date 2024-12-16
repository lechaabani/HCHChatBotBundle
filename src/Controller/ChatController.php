<?php

namespace HCH\ChatBotBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use HCH\ChatBotBundle\Service\ChatBotService;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'hch_chatbot_chat', methods: ['POST'])]
    public function chat(Request $request, ChatBotService $chatBot): JsonResponse
    {
        $message = $request->request->get('message');
        
        try {
            $response = $chatBot->sendMessage($message);
            return $this->json(['response' => $response]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
} 