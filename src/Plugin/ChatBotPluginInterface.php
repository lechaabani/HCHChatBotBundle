<?php

namespace HCH\ChatBotBundle\Plugin;

interface ChatBotPluginInterface
{
    public function getName(): string;
    
    public function processMessage(string $message, array $context = []): string;
    
    public function getPriority(): int;
    
    public function isEnabled(): bool;
} 