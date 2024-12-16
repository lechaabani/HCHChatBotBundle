<?php

namespace HCH\ChatBotBundle\EventListener;

use HCH\ChatBotBundle\Event\ChatMessageProcessedEvent;
use HCH\ChatBotBundle\Service\AnalyticsService;

class ChatMessageListener
{
    public function __construct(
        private AnalyticsService $analytics
    ) {}

    public function onChatMessageProcessed(ChatMessageProcessedEvent $event): void
    {
        $this->analytics->trackConversation(
            $event->getMessage(),
            $event->getResponse(),
            $event->getContext()
        );
    }
} 