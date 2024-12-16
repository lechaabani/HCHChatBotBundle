<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use HCH\ChatBotBundle\Service\ChatBotService;

class ChatController extends AbstractController
{
    public function chat(Request $request, ChatBotService $chatBot): Response
    {
        $message = $request->request->get('message');
        
        try {
            $response = $chatBot->sendMessage($message, [
                'template' => 'conversation',
                'history' => $request->getSession()->get('chat_history', [])
            ]);
            
            return $this->json(['response' => $response]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    public function translate(Request $request, ChatBotService $chatBot): Response
    {
        $text = $request->request->get('text');
        $targetLang = $request->request->get('target_language');
        
        try {
            $response = $chatBot->sendMessage($text, [
                'template' => 'translation',
                'language' => $targetLang
            ]);
            
            return $this->json(['translation' => $response]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
} 