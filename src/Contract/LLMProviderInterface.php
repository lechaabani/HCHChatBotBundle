<?php

namespace HCH\ChatBotBundle\Contract;

interface LLMProviderInterface
{
    public function sendMessage(string $message, array $context = []): string;
    public function getModelName(): string;
    public function getModelCapabilities(): array;
    public function validateConfiguration(): bool;
    public function getTokenCount(string $text): int;
    public function formatPrompt(string $message, array $context = []): array;
    public function getRemainingQuota(): ?int;
    public function hasReachedLimit(): bool;
} 