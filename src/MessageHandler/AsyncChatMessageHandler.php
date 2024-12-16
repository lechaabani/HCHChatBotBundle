<?php

namespace HCH\ChatBotBundle\MessageHandler;

use HCH\ChatBotBundle\Message\AsyncChatMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AsyncChatMessageHandler implements MessageHandlerInterface
{
    private $chatService;

    public function __construct(ChatBotService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function __invoke(AsyncChatMessage $message)
    {
        $this->chatService->processMessage($message->getMessage(), $message->getContext());
    }
} 