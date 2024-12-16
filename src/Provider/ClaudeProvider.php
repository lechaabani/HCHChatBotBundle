<?php

namespace HCH\ChatBotBundle\Provider;

use HCH\ChatBotBundle\Contract\PromptFormatterInterface;
use HCH\ChatBotBundle\Exception\ProviderException;
use HCH\ChatBotBundle\Exception\QuotaExceededException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ClaudeProvider extends AbstractLLMProvider
{
    private const TOKEN_LIMIT = 100000;

    public function __construct(
        array $config,
        PromptFormatterInterface $promptFormatter,
        HttpClientInterface $httpClient
    ) {
        parent::__construct($config, $promptFormatter, $httpClient);
    }

    public function sendMessage(string $message, array $context = []): string
    {
        if ($this->hasReachedLimit()) {
            throw new QuotaExceededException('Daily quota exceeded for Claude provider');
        }

        $formattedPrompt = $this->formatPrompt($message, $context);
        
        if ($this->getTokenCount($message) > self::TOKEN_LIMIT) {
            throw new ProviderException('Message exceeds token limit');
        }

        try {
            $response = $this->executeRequest($formattedPrompt);
            $this->incrementUsage();
            return $response;
        } catch (\Exception $e) {
            throw new ProviderException('Claude Error: ' . $e->getMessage());
        }
    }

    public function getModelName(): string
    {
        return $this->config['model'] ?? 'claude-2';
    }

    public function getTokenCount(string $text): int
    {
        // Claude utilise un tokenizer spécifique
        // Implémentation approximative basée sur les caractères
        return (int) (strlen($text) / 3.5);
    }

    protected function executeRequest(array $formattedPrompt): string
    {
        $response = $this->httpClient->request('POST', 'https://api.anthropic.com/v1/messages', [
            'headers' => [
                'x-api-key' => $this->config['api_key'],
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ],
            'json' => [
                'model' => $this->getModelName(),
                'messages' => $formattedPrompt,
                'max_tokens' => $this->config['max_tokens'] ?? 1000,
            ]
        ]);

        $data = $response->toArray();
        return $data['content'][0]['text'] ?? throw new ProviderException('Invalid Claude response format');
    }

    public function getModelCapabilities(): array
    {
        return [
            'max_tokens' => self::TOKEN_LIMIT,
            'supports_streaming' => true,
            'supports_functions' => false,
            'supports_vision' => false
        ];
    }

    public function validateConfiguration(): bool
    {
        if (empty($this->config['api_key'])) {
            throw new ProviderException('Claude API key is required');
        }

        return true;
    }

    public function getRemainingQuota(): ?int
    {
        if (!isset($this->limits['daily_requests'])) {
            return null;
        }
        return $this->limits['daily_requests'] - $this->usageCount;
    }
} 