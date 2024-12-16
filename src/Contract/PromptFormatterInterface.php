<?php

namespace HCH\ChatBotBundle\Contract;

interface PromptFormatterInterface
{
    public function formatPrompt(string $message, array $context = []): array;
} 