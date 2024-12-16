<?php

namespace HCH\ChatBotBundle\Provider;

use HCH\ChatBotBundle\Contract\PromptFormatterInterface;
use HCH\ChatBotBundle\Exception\ProviderException;
use HCH\ChatBotBundle\Exception\QuotaExceededException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaLMProvider extends AbstractLLMProvider
{
    private const TOKEN_LIMIT = 8192;

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
            throw new QuotaExceededException('Daily quota exceeded for PaLM provider');
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
            throw new ProviderException('PaLM Error: ' . $e->getMessage());
        }
    }

    public function getModelName(): string
    {
        return $this->config['model'] ?? 'chat-bison-001';
    }

    protected function executeRequest(array $formattedPrompt): string
    {
        $response = $this->httpClient->request('POST', 'https://generativelanguage.googleapis.com/v1beta/models/chat-bison-001:generateText', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config['api_key'],
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'prompt' => ['messages' => $formattedPrompt],
                'temperature' => $this->config['temperature'] ?? 0.7,
                'candidate_count' => 1,
            ]
        ]);

        $data = $response->toArray();
        return $data['candidates'][0]['content'] ?? throw new ProviderException('Invalid PaLM response format');
    }

    public function getTokenCount(string $text): int
    {
        // Implémentation approximative basée sur les caractères pour PaLM
        return (int) (strlen($text) / 3.5);
    }

    public function validateConfiguration(): bool
    {
        if (empty($this->config['api_key'])) {
            throw new ProviderException('PaLM API key is required');
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

    public function getModelCapabilities(): array
    {
        return [
            'max_tokens' => self::TOKEN_LIMIT,
            'supports_streaming' => false,
            'supports_functions' => true,
            'supports_vision' => false
        ];
    }
} 