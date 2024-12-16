<?php

namespace HCH\ChatBotBundle\Contract;

interface PromptFormatterInterface
{
    public function format(string $message, array $context = []): array;
    public function addSystemPrompt(string $prompt): void;
    public function addTemplate(string $name, string $template): void;
} 