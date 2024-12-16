<?php

namespace HCH\ChatBotBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ChatMessageEvent extends Event
{
    public const PRE_PROCESS = 'HCHChatBotBundle.message.pre_process';
    public const POST_PROCESS = 'HCHChatBotBundle.message.post_process';

    private string $message;
    private array $context;
    private ?string $response = null;

    public function __construct(string $message, array $context = [])
    {
        $this->message = $message;
        $this->context = $context;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): self
    {
        $this->context = $context;
        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(string $response): self
    {
        $this->response = $response;
        return $this;
    }
} 