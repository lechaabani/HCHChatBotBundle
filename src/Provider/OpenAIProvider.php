<?php

namespace HCH\ChatBotBundle\Provider;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use HCH\ChatBotBundle\Exception\ProviderException;
use HCH\ChatBotBundle\Exception\QuotaExceededException;

class OpenAIProvider extends AbstractLLMProvider
{
    private const TOKEN_LIMIT = 4096;
    private HttpClientInterface $httpClient;

    public function __construct(
        array $config,
        PromptFormatterInterface $promptFormatter,
        HttpClientInterface $httpClient
    ) {
        parent::__construct($config, $promptFormatter);
        $this->httpClient = $httpClient;
    }

    public function sendMessage(string $message, array $context = []): string
    {
        if ($this->hasReachedLimit()) {
            throw new QuotaExceededException('Daily quota exceeded for OpenAI provider');
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
            throw new ProviderException('OpenAI Error: ' . $e->getMessage());
        }
    }

    public function getTokenCount(string $text): int
    {
        // Utilisation de la bibliothèque tiktoken pour un comptage précis
        $encoding = \TiktokenEncoder::encoding_for_model($this->config['model']);
        return count($encoding->encode($text));
    }

    public function getModelCapabilities(): array
    {
        return [
            'max_tokens' => self::TOKEN_LIMIT,
            'supports_streaming' => true,
            'supports_functions' => true,
            'supports_vision' => $this->config['model'] === 'gpt-4-vision-preview'
        ];
    }

    protected function executeRequest(array $formattedPrompt): string
    {
        $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config['api_key'],
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => $this->config['model'],
                'messages' => $formattedPrompt,
                'temperature' => $this->config['temperature'] ?? 0.7,
                'max_tokens' => $this->config['max_tokens'] ?? 1000,
            ]
        ]);

        $data = $response->toArray();
        if (!isset($data['choices'][0]['message']['content'])) {
            throw new ProviderException('Invalid response format from OpenAI');
        }

        return $data['choices'][0]['message']['content'];
    }

    public function validateConfiguration(): bool
    {
        if (empty($this->config['api_key'])) {
            throw new ProviderException('OpenAI API key is required');
        }

        if (empty($this->config['model'])) {
            throw new ProviderException('OpenAI model must be specified');
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