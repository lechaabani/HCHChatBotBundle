<?php

namespace HCH\ChatBotBundle\Provider;

use HCH\ChatBotBundle\Contract\LLMProviderInterface;
use HCH\ChatBotBundle\Contract\PromptFormatterInterface;
use HCH\ChatBotBundle\Exception\QuotaExceededException;
use HCH\ChatBotBundle\Exception\ProviderException;

abstract class AbstractLLMProvider implements LLMProviderInterface
{
    protected array $config;
    protected PromptFormatterInterface $promptFormatter;
    protected array $limits = [];
    protected int $usageCount = 0;

    public function __construct(
        array $config,
        PromptFormatterInterface $promptFormatter
    ) {
        $this->config = $config;
        $this->promptFormatter = $promptFormatter;
        $this->limits = $config['limits'] ?? [];
        $this->validateConfiguration();
    }

    public function hasReachedLimit(): bool
    {
        if (isset($this->limits['daily_requests']) && $this->usageCount >= $this->limits['daily_requests']) {
            return true;
        }
        return false;
    }

    protected function incrementUsage(): void
    {
        $this->usageCount++;
    }

    public function formatPrompt(string $message, array $context = []): array
    {
        return $this->promptFormatter->formatPrompt($message, $context);
    }

    abstract public function getTokenCount(string $text): int;
    abstract protected function executeRequest(array $formattedPrompt): string;
} 