<?php

namespace HCH\ChatBotBundle\Message;

class AsyncChatMessage
{
    private $message;
    private $context;

    public function __construct(string $message, array $context = [])
    {
        $this->message = $message;
        $this->context = $context;
    }

    // Getters...
} 