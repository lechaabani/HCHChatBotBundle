<?php

namespace HCH\ChatBotBundle\Provider;

interface LLMProviderInterface
{
    public function getName(): string;
    public function getResponse(string $message, array $context = []): string;
    public function isAvailable(): bool;
    public function getConfig(): array;
} 