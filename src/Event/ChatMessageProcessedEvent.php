<?php

namespace HCH\ChatBotBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ChatMessageProcessedEvent extends Event
{
    public function __construct(
        private string $message,
        private string $response,
        private array $context = []
    ) {}

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function getContext(): array
    {
        return $this->context;
    }
} 